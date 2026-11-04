<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
use App\Models\Post\Post;
use App\Models\Product\Product;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Wishlist;
use App\Services\SmsOtpService;
use App\Services\ZaloZNSService;
use Exception;
use Illuminate\Support\Facades\Redirect;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Laravel\Socialite\Facades\Socialite;


class UserAuthController extends Controller
{
    /**
     * Redirect to Google OAuth consent screen.
     */
    public function redirectToGoogle()
    {
        $redirectUrl = config('services.google.redirect') ?: route('auth.google.callback');
        return Socialite::driver('google')
            ->redirectUrl($redirectUrl)
            ->redirect();
    }

    /**
     * Handle Google OAuth callback: find or create user, login, redirect to profile.
     */
    public function handleGoogleCallback()
    {
        $redirectUrl = config('services.google.redirect') ?: route('auth.google.callback');
        Log::info('Google OAuth callback started', ['redirect_uri' => $redirectUrl]);

        try {
            $googleUser = Socialite::driver('google')
                ->redirectUrl($redirectUrl)
                ->user();
        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            Log::warning('Google OAuth invalid state', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('user.login')
                ->withErrors(['login' => 'Phiên đăng nhập Google không hợp lệ. Vui lòng thử lại.']);
        } catch (\Throwable $e) {
            Log::error('Google OAuth error', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('user.login')
                ->withErrors(['login' => 'Đăng nhập bằng Google thất bại. Vui lòng thử lại sau.']);
        }

        $googleId = $googleUser->getId();
        $email = $googleUser->getEmail();
        $name = $googleUser->getName() ?: $googleUser->getNickname() ?: 'User';
        Log::info('Google OAuth user data received', ['google_id' => $googleId, 'email' => $email, 'name' => $name]);

        if (empty($email)) {
            Log::warning('Google OAuth: email not provided', ['google_id' => $googleId, 'name' => $name]);
            return redirect()->route('user.login')
                ->withErrors(['login' => 'Tài khoản Google không cung cấp email. Vui lòng đăng ký bằng email.']);
        }

        try {
            $user = User::where('google_id', $googleId)->first();
            if ($user) {
                Log::info('Google OAuth: existing user by google_id', ['user_id' => $user->id, 'email' => $user->email]);
            } else {
                $user = User::where('email', $email)->first();
                if ($user) {
                    $user->update(['google_id' => $googleId]);
                    Log::info('Google OAuth: linked existing user by email', ['user_id' => $user->id, 'email' => $email]);
                } else {
                    $user = User::create([
                        'name' => $name,
                        'email' => $email,
                        'google_id' => $googleId,
                        'password' => Hash::make(Str::random(32)),
                    ]);
                    Log::info('Google OAuth: new user created', ['user_id' => $user->id, 'email' => $email]);
                }
            }

            $request = request();
            Auth::guard('user')->login($user);
            $request->session()->regenerate();

            Log::info('Google OAuth: login success', ['user_id' => $user->id]);
            return Inertia::location(route('users.profile.index'));
        } catch (\Throwable $e) {
            Log::error('Google OAuth: find/create user or login failed', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'google_id' => $googleId ?? null,
                'email' => $email ?? null,
            ]);
            return redirect()->route('user.login')
                ->withErrors(['login' => 'Đăng nhập bằng Google thất bại. Vui lòng thử lại sau.']);
        }
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'login' => ['required', 'string'],
                'password' => ['required', 'string', 'min:8'],
            ], [
                'login.required' => 'Vui lòng nhập email hoặc số điện thoại.',
                'password.required' => 'Vui lòng nhập mật khẩu.',
                'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            ]);

            $login = trim((string) $request->input('login'));
            $password = $request->input('password');

            // Xác định đăng nhập bằng email hay số điện thoại
            $isEmail = str_contains($login, '@');
            if ($isEmail) {
                $request->validate([
                    'login' => ['email'],
                ], [
                    'login.email' => 'Email không đúng định dạng.',
                ]);
                $credentials = ['email' => $login, 'password' => $password];
            } else {
                $request->validate([
                    'login' => ['regex:/^[0-9]{10,11}$/'],
                ], [
                    'login.regex' => 'Số điện thoại phải có 10 hoặc 11 chữ số.',
                ]);
                $credentials = ['phone' => $login, 'password' => $password];
            }

            if (Auth::guard('user')->attempt($credentials)) {
                $request->session()->regenerate();
                $user = Auth::guard('user')->user();
                return Inertia::location(route('users.profile.index'));
            }

            return back()->withErrors([
                'login' => 'Email/số điện thoại hoặc mật khẩu không chính xác.',
            ])->onlyInput('login');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->onlyInput('login');
        } catch (Exception $e) {
            Log::error('Login error: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all(),
            ]);
            return back()->withErrors([
                'login' => 'Đã xảy ra lỗi trong quá trình đăng nhập. Vui lòng thử lại sau.',
            ])->onlyInput('login');
        }
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255', 'regex:/^[0-9]{10,11}$/', 'unique:users,phone'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ], [
            'name.required' => 'Vui lòng nhập họ và tên.',
            'name.string' => 'Họ và tên phải là chuỗi ký tự.',
            'name.max' => 'Họ và tên không được vượt quá 255 ký tự.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.string' => 'Số điện thoại phải là chuỗi ký tự.',
            'phone.max' => 'Số điện thoại không được vượt quá 255 ký tự.',
            'phone.regex' => 'Số điện thoại phải có 10 hoặc 11 chữ số.',
            'phone.unique' => 'Số điện thoại đã được sử dụng.',
            'email.required' => 'Vui lòng nhập email.',
            'email.string' => 'Email phải là chuỗi ký tự.',
            'email.email' => 'Email không đúng định dạng.',
            'email.max' => 'Email không được vượt quá 255 ký tự.',
            'email.unique' => 'Email đã được sử dụng.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.string' => 'Mật khẩu phải là chuỗi ký tự.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::guard('user')->login($user);

        return Inertia::location(route('users.profile.index'));
    }




    public function logout(Request $request)
    {
        Auth::guard('user')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = Auth::guard('user')->user();

        if (!$user) {
            return back()->withErrors(['user' => 'Không tìm thấy người dùng']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->back()->with('success', 'Cập nhật mật khẩu thành công.');
    }

    public function createPost(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'province_code' => 'required|string|max:100',
            'district_code' => 'required|string|max:100',
            'unit_price' => 'required|string|max:100',
            'negotiable_price' => 'nullable|boolean',
            'street' => 'nullable|string|max:255',
            'streets' => 'nullable|string|max:255',
            'houseNumber' => 'nullable|string|max:100',
            'price' => 'nullable|numeric',
            'price_rent' => 'nullable|numeric',
            'bed_room' => 'nullable|numeric',
            'toilet' => 'nullable|numeric',
            'exact_address' => 'nullable|string|max:255',
            'video_url' => 'nullable|url',
            'video' => 'nullable|url',
            'images' => 'required|array|min:1',
            'images.*' => 'url', // Validate URLs
            'direction' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'acreage' => 'nullable|numeric',
            'project_id' => 'nullable|numeric',
            'transaction_type_id' => 'required|numeric',
            'product_category_id' => 'required|numeric',
        ], [
            'title.required' => 'Vui lòng nhập tiêu đề.',
            'description.required' => 'Vui lòng nhập mô tả.',
            'province_code.required' => 'Tỉnh/thành phố là bắt buộc.',
            'district_code.required' => 'Quận/huyện là bắt buộc.',
            'transaction_type_id.required' => 'Loại giao dịch là bắt buộc.',
            'product_category_id.required' => 'Danh mục sản phẩm là bắt buộc.',
            'video_url.url' => 'Liên kết video không đúng định dạng.',
            'video.mimes' => 'Video chỉ được định dạng mp4, avi hoặc mov.',
            'images.required' => 'Vui lòng chọn ít nhất 1 hình ảnh.',
            'images.*.url' => 'Hình ảnh phải là URL hợp lệ.',
        ]);

        $userId = auth()->id();

        $validated['user_id'] = $userId;
        $validated['sku'] = Product::generateUniqueSku();

        // Lưu video nếu có
        if (!empty($validated['video'])) {
            $validated['video'] = [
                'path' => $validated['video'],
                'type' => 'file',
                'filename' => basename($validated['video']),
                'extension' => pathinfo($validated['video'], PATHINFO_EXTENSION),
                'file_size' => 0,
                'mime_type' => 'video/' . pathinfo($validated['video'], PATHINFO_EXTENSION),
                'visibility' => 'public',
                'extra_metadata' => [],
                'formatted_file_size' => '0 KB',
            ];
        }

        // Store image URLs directly
        $validated['images'] = array_map(function ($url) {
            return [
                'path' => $url,
                'type' => 'file',
                'filename' => basename($url),
                'extension' => pathinfo($url, PATHINFO_EXTENSION),
                'file_size' => 0, // Size not needed since already uploaded
                'mime_type' => 'image/' . pathinfo($url, PATHINFO_EXTENSION),
                'visibility' => 'public',
                'extra_metadata' => [],
                'formatted_file_size' => '0 KB',
            ];
        }, $validated['images']);

        Product::create($validated);

        return Redirect::route('users.post.index')->with('success', 'Tạo bài đăng thành công!');
    }

    public function editPost($id)
    {
        try {
            $product = Product::where('id', $id)->firstOrFail()
                ->transformDetails();

            return Inertia::render('Users/Posts/Show', ['product' => $product]);
        } catch (\Throwable $th) {
            Log::error('Update post failed: ' . $th->getMessage());
            return Redirect::back()->with('error', 'Cập nhật trạng thái thất bại. Vui lòng thử lại.');
        }
    }

    public function updatePost(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'province_code' => 'required|string|max:100',
            'district_code' => 'required|string|max:100',
            'unit_price' => 'required|string|max:100',
            'negotiable_price' => 'nullable|boolean',
            'street' => 'nullable|string|max:255',
            'streets' => 'nullable|string|max:255',
            'houseNumber' => 'nullable|string|max:100',
            'price' => 'nullable|numeric',
            'price_rent' => 'nullable|numeric',
            'bed_room' => 'nullable|numeric',
            'toilet' => 'nullable|numeric',
            'exact_address' => 'nullable|string|max:255',
            'video_url' => 'nullable|url',
            'video' => 'nullable|url',
            'images' => 'required|array|min:1',
            'images.*' => 'url',
            'direction' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'acreage' => 'nullable|numeric',
        ], [
            'title.required' => 'Vui lòng nhập tiêu đề.',
            'description.required' => 'Vui lòng nhập mô tả.',
            'province_code.required' => 'Tỉnh/thành phố là bắt buộc.',
            'district_code.required' => 'Quận/huyện là bắt buộc.',
            'transaction_type_id.required' => 'Loại giao dịch là bắt buộc.',
            'product_category_id.required' => 'Danh mục sản phẩm là bắt buộc.',
            'video_url.url' => 'Liên kết video không đúng định dạng.',
            'video.mimes' => 'Video chỉ được định dạng mp4, avi hoặc mov.',
            'images.required' => 'Vui lòng chọn ít nhất 1 hình ảnh.',
            'images.*.url' => 'Hình ảnh phải là URL hợp lệ.',
        ]);

        // Lưu video nếu có
        if (!empty($validated['video'])) {
            $validated['video'] = [
                'path' => $validated['video'],
                'type' => 'file',
                'filename' => basename($validated['video']),
                'extension' => pathinfo($validated['video'], PATHINFO_EXTENSION),
                'file_size' => 0,
                'mime_type' => 'video/' . pathinfo($validated['video'], PATHINFO_EXTENSION),
                'visibility' => 'public',
                'extra_metadata' => [],
                'formatted_file_size' => '0 KB',
            ];
        }

        // Store image URLs directly
        $validated['images'] = array_map(function ($url) {
            return [
                'path' => $url,
                'type' => 'file',
                'filename' => basename($url),
                'extension' => pathinfo($url, PATHINFO_EXTENSION),
                'file_size' => 0, // Size not needed since already uploaded
                'mime_type' => 'image/' . pathinfo($url, PATHINFO_EXTENSION),
                'visibility' => 'public',
                'extra_metadata' => [],
                'formatted_file_size' => '0 KB',
            ];
        }, $validated['images']);

        $product->update($validated);

        return Redirect::back()
            ->with('success', 'Cập nhật bài đăng thành công!');
    }

    public function showProfile()
    {
        $user = Auth::guard('user')->user();

        return Inertia::render('Users/Profile/Index', [
            'schools' => [],
            'majors' => [],
            'currentStatusOptions' => [],
            'schoolYears' => [],
            'rankMilestones' => [],
            'ranks' => [],
            'userRank' => null,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::guard('user')->user();

        $validated = $request->validate([
            'name'           => 'required|string|min:2|max:255',
            'email'          => 'required|email|unique:users,email,' . $user->id,
            'phone'          => ['required', 'string', 'regex:/^0[35789][0-9]{8}$/'],
            'zalo'           => ['nullable', 'string'],
            'status'         => 'nullable|in:ACTIVE,INACTIVE',
            'address'        => 'nullable|string|max:255',
            'note'           => 'nullable|string|max:1000',
            'avatar'         => 'nullable|string',
            'password'       => 'nullable|string|min:8|confirmed',
        ], [
            'name.required'          => 'Vui lòng nhập họ tên.',
            'name.min'               => 'Họ tên phải có ít nhất 2 ký tự.',
            'name.max'               => 'Họ tên không được vượt quá :max ký tự.',
            'email.required'         => 'Vui lòng nhập email.',
            'email.email'            => 'Email không đúng định dạng.',
            'email.unique'           => 'Email đã tồn tại trong hệ thống.',
            'phone.required'         => 'Vui lòng nhập số điện thoại (Zalo).',
            'phone.regex'            => 'Số điện thoại không hợp lệ (Phải gồm 10 chữ số, VD: 0938039170).',
            'password.min'           => 'Mật khẩu phải có ít nhất :min ký tự.',
            'password.confirmed'     => 'Xác nhận mật khẩu không khớp.',
        ], [
            'name'           => 'họ tên',
            'email'          => 'email',
            'phone'          => 'số điện thoại',
            'zalo'           => 'Link Facebook',
            'status'         => 'trạng thái',
            'address'        => 'địa chỉ',
            'note'           => 'ghi chú',
            'avatar'         => 'ảnh đại diện',
            'password'       => 'mật khẩu',
        ]);

        // Xử lý avatar
        if (!empty($validated['avatar'])) {
            $validated['avatar'] = [
                'path'              => $validated['avatar'],
                'type'              => 'file',
                'filename'          => basename($validated['avatar']),
                'extension'         => pathinfo($validated['avatar'], PATHINFO_EXTENSION),
                'file_size'         => 0,
                'mime_type'         => 'image/' . pathinfo($validated['avatar'], PATHINFO_EXTENSION),
                'visibility'        => 'public',
                'extra_metadata'    => [],
                'formatted_file_size' => '0 KB',
            ];
        }

        // Xử lý mật khẩu
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        // Cập nhật thông tin người dùng
        $user->update($validated);

        $message = 'Cập nhật hồ sơ thành công!';

        return redirect()
            ->back()
            ->with('success', $message);
    }

    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'avatar.required' => 'Vui lòng chọn hình ảnh để tải lên.',
            'avatar.image' => 'File tải lên phải là hình ảnh.',
            'avatar.mimes' => 'Hình ảnh đại diện chỉ hỗ trợ định dạng jpeg, png, jpg, gif, webp.',
            'avatar.max' => 'Dung lượng hình ảnh tối đa là 2MB.',
        ]);

        try {
            $user = Auth::guard('user')->user();
            $file = $request->file('avatar');
            
            $fileObj = new \App\Models\File('UserAvatar');
            $fileUploaded = $fileObj->store([$file]);

            if (isset($fileUploaded['successFiles']) && !empty($fileUploaded['successFiles'])) {
                $filePath = str_replace('/static/', '', $fileUploaded['successFiles'][0]);
                
                $avatarJson = [
                    'path'              => $filePath,
                    'type'              => 'file',
                    'filename'          => basename($filePath),
                    'extension'         => pathinfo($filePath, PATHINFO_EXTENSION),
                    'file_size'         => $file->getSize(),
                    'mime_type'         => $file->getClientMimeType(),
                    'visibility'        => 'public',
                    'extra_metadata'    => [],
                    'formatted_file_size' => '0 KB',
                ];

                $user->update(['avatar' => $avatarJson]);

                return response()->json([
                    'success' => true,
                    'avatar' => $user->getImageDetail($avatarJson),
                    'message' => 'Cập nhật ảnh đại diện thành công!'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Tải ảnh lên thất bại.'
            ], 422);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi hệ thống: ' . $th->getMessage()
            ], 500);
        }
    }

    public function getListPost()
    {
        $user = Auth::guard('user')->user();

        $products = Product::query()
            ->where('user_id', $user->id)
            ->filter(request()->all())
            ->orderByPosition()
            ->paginate(3) // Fix: gọi paginate() trực tiếp, không qua get() trước
            ->through(function ($item) {
                return $item->transform();
            })->withQueryString();

        $data = [
            'products' => $products,
        ];

        if (request()->wantsJson()) {
            return response()->json($data);
        }

        return Inertia::render('Users/Posts/Index', $data);
    }

    public function updatePostStatus($id, Request $request)
    {
        $request->validate([
            'status' => 'required|in:' . Product::STATUS_ACTIVE . ',' . Product::STATUS_INACTIVE,
        ]);

        try {
            $user = Auth::guard('user')->user();

            $product = Product::where('id', $id)
                ->where('user_id', $user->id)
                ->firstOrFail();

            $product->status = $request->status;
            $product->save();

            $message = $request->status === Product::STATUS_ACTIVE
                ? 'Bài đăng đã được kích hoạt thành công!'
                : 'Bài đăng đã được ẩn thành công!';

            $data = [
                'message' => $message,
                'product' => $product
            ];

            if ($request->wantsJson()) {
                return response()->json($data);
            }

            return Redirect::route('users.post.list')->with('success', $message);
        } catch (\Throwable $th) {
            return response()->json([
                'error'   => 'Có lỗi xảy ra khi cập nhật trạng thái bài đăng.',
                'details' => $th->getMessage()
            ], 500);
        }
    }

    public function toggleWishlist(Request $request)
    {
        try {
            $user = Auth::guard('user')->user();
            if (!$user) {
                return redirect()->route('user.login')->with('flash.message', [
                    'message' => 'Bạn cần đăng nhập để thực hiện thao tác này',
                    'type' => 'error',
                ]);
            }

            $productId = $request->input('product_id');
            if (!$productId) {
                return redirect()->back()->with('flash.message', [
                    'message' => 'Product ID is required',
                    'type' => 'error',
                ]);
            }

            // Check if the product is already in the wishlist
            $existingWishlist = Wishlist::where('user_id', $user->id)
                ->where('product_id', $productId)
                ->first();
            if ($existingWishlist) {
                // Remove from wishlist
                $existingWishlist->delete();
                return redirect()->back()->with('flash.message', [
                    'message' => 'Xóa khỏi wishlist thành công',
                    'in_wishlist' => false,
                    'type' => 'success',
                ]);
            }

            // Add to wishlist
            Wishlist::create([
                'user_id' => $user->id,
                'product_id' => $productId,
            ]);

            return redirect()->back()->with('flash.message', [
                'message' => 'Thêm vào wishlist thành công',
                'in_wishlist' => true,
                'type' => 'success',
            ]);
        } catch (\Throwable $th) {
            \Log::error('Wishlist toggle failed: ' . $th->getMessage());
            return redirect()->back()->with('flash.message', [
                'message' => 'Đã xảy ra lỗi khi thực hiện thao tác',
                'type' => 'error',
            ]);
        }
    }

    public function getWishlist()
    {
        $user = Auth::guard('user')->user();

        if (!$user) {
            return redirect()->route('user.login');
        }

        $productIds = Wishlist::where('user_id', $user->id)
            ->pluck('product_id');

        $products = Product::whereIn('id', $productIds)
            ->orderByPosition()
            ->get()
            ->paginate(4)
            ->through(function ($item) {
                return $item->transform();
            })->withQueryString();

        return Inertia::render('Users/Posts/Wishlist', [
            'products' => $products,
        ]);
    }

    // Gửi link reset qua email
    public function sendResetPass(Request $request, SmsOtpService $smsService, ZaloZNSService $zaloZNSService)
    {
        try {
            $method = $request->input('method');

            if ($method === 'sms') {
                // Kiểm tra phone có tồn tại không
                $phone = $request->input('phone');
                $user = User::where('phone', $phone)->first();

                if (!$user) {
                    return back()->withErrors(['phone' => 'Số điện thoại này chưa được đăng ký.']);
                }

                return $this->sendOtpSMS($request, $smsService);
            }

            if ($method === 'zalo') {

                // Kiểm tra phone có tồn tại không
                $phone = $request->input('phone');
                $user = User::where('phone', $phone)->first();

                if (!$user) {
                    return back()->withErrors(['phone' => 'Số điện thoại này chưa được đăng ký.']);
                }

                return $this->sendOtpZnS($request, $zaloZNSService);
            }

            if ($method === 'email') {

                // Kiểm tra email có tồn tại không
                $email = $request->input('email');
                $user = User::where('email', $email)->first();

                if (!$user) {
                    return back()->withErrors(['email' => 'Email này chưa được đăng ký.']);
                }

                return $this->sendEmailReset($request);
            }

            return back()->withErrors(['method' => 'Phương thức không hợp lệ.']);
        } catch (\Throwable $th) {
            report($th);
            return back()->withErrors(['error' => 'Đã xảy ra lỗi. Vui lòng thử lại.']);
        }
    }


    protected function sendOtpSMS(Request $request, SmsOtpService $smsService)
    {
        $request->validate([
            'phone' => 'required|regex:/^0[0-9]{9}$/'
        ]);

        $phone = $request->input('phone');
        $otp = rand(100000, 999999);

        // Cache OTP trong 3 phút
        Cache::put('otp_' . $phone . '_' . app()->getLocale(), $otp, now()->addMinutes(3));

        // Gửi OTP qua SMS
        $smsService->sendOtp($phone, $otp);

        return redirect()->back()->with('success', 'Đã gửi mã OTP tới số điện thoại của bạn');
    }

    protected function sendOtpZnS(Request $request, ZaloZNSService $zaloZNSService)
    {
        $request->validate([
            'phone' => 'required|regex:/^0[0-9]{9}$/'
        ]);

        $phone = $request->input('phone');
        $otp = rand(100000, 999999);

        // Cache OTP trong 3 phút
        Cache::put('otp_' . $phone . '_' . app()->getLocale(), $otp, now()->addMinutes(3));

        // Gửi OTP qua SMS
        $zaloZNSService->sendOtp($phone, $otp);

        return redirect()->back()->with('success', 'Đã gửi mã OTP tới số điện thoại của bạn');
    }

    public function verifyOtpSMS(Request $request)
    {
        try {
            $validated = $request->validate([
                'phone' => 'required|regex:/^0[0-9]{9}$/',
                'otp' => 'required|digits:6',
            ]);

            $phone = $validated['phone'];
            $otp = $validated['otp'];
            $cachedOtp = Cache::get('otp_' . $phone . '_' . app()->getLocale()) ?? Cache::get('otp_' . $phone);

            if ($cachedOtp && $otp == $cachedOtp) {
                foreach (config('app.locales', ['vi', 'en']) as $locale) {
                    Cache::forget('otp_' . $phone . '_' . $locale);
                }
                Cache::forget('otp_' . $phone);

                session(['otp_verified_phone' => $phone]);

                return Redirect::route('password.reset.form', ['phone' => $phone])
                    ->with('success', 'Xác thực OTP thành công. Hãy đặt lại mật khẩu.');
            }

            return Redirect::back()->withErrors(['otp' => 'Mã OTP không chính xác hoặc đã hết hạn.']);
        } catch (\Throwable $th) {
            report($th);
            return back()->withErrors(['error' => 'Xảy ra lỗi xác thực. Vui lòng thử lại.']);
        }
    }

    protected function sendEmailReset(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $status = Password::broker('users')->sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return redirect()->back()->with('success', __($status));
        }

        return redirect()->back()->withErrors(['email' => __($status)]);
    }

    public function showResetForm(Request $request, $token)
    {
        return Inertia::render('Users/ResetPassword', [
            'token' => $token,
            'email' => $request->query('email')
        ]);
    }

    // Cập nhật lại mật khẩu
    public function resetPass(Request $request)
    {
        $method = $request->input('method');

        if ($method === 'sms') {
            // Đảm bảo đã xác thực OTP trước đó
            $phone = session('otp_verified_phone');
            if (!$phone) {
                return redirect()->back()->withErrors(['otp' => 'Bạn chưa xác thực OTP.']);
            }

            $request->validate([
                'password' => 'required|min:8|confirmed',
            ]);

            $user = User::where('phone', $phone)->first();
            if (!$user) {
                return redirect()->back()->withErrors(['phone' => 'Không tìm thấy tài khoản tương ứng với số điện thoại.']);
            }

            $user->forceFill([
                'password' => Hash::make($request->input('password')),
                'remember_token' => Str::random(60),
            ])->save();

            // Xoá session để tránh reuse
            session()->forget('otp_verified_phone');

            return redirect()->route('user.login')->with('status', 'Đặt lại mật khẩu thành công.');
        }

        if ($method === 'email') {
            // Nếu là email thì xử lý theo mặc định
            $request->validate([
                'token'    => 'required',
                'email'    => 'required|email',
                'password' => 'required|min:8|confirmed',
            ]);

            $status = Password::broker('users')->reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    $user->forceFill([
                        'password' => Hash::make($password),
                        'remember_token' => Str::random(60),
                    ])->save();
                }
            );

            return $status === Password::PASSWORD_RESET
                ? redirect()->route('user.login')->with('status', __($status))
                : back()->withErrors(['email' => [__($status)]]);
        }
    }


    public function resetPasswordSMS(Request $request)
    {
        $request->validate([
            'phone' => 'required_without:email',
            'password' => 'required|confirmed|min:6',
        ]);

        if ($request->filled('phone')) {
            $user = \App\Models\User::where('phone', $request->phone)->first();
        } else {
            $user = \App\Models\User::where('email', $request->email)->first();
        }

        if (!$user) {
            return back()->withErrors(['account' => 'Tài khoản không tồn tại.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        session()->forget('otp_verified_phone');

        return redirect()->route('login')->with('success', 'Mật khẩu đã được đặt lại. Bạn có thể đăng nhập.');
    }

    public function fptSmsCallback(Request $request)
    {
        \Log::info('FPT SMS Callback - Request Received', ['body' => $request->all()]);

        try {
            // Xác thực Authorization
            $authHeader = $request->header('Authorization');
            $expectedAuth = 'Basic ' . config('sms.fpt_sms.callback_auth');

            if ($authHeader !== $expectedAuth) {
                \Log::warning('FPT SMS Callback: Unauthorized access attempt', [
                    'provided' => $authHeader
                ]);
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            // Lấy dữ liệu từ request
            $data = $request->only([
                'smsid',
                'status',
                'telco',
                'error',
                'phone',
                'brandname',
                'mt_count',
                'request_id'
            ]);

            // Kiểm tra bắt buộc
            if (!isset($data['smsid'], $data['status'])) {
                \Log::error('FPT SMS Callback: Missing required fields', $data);
                return response()->json(['message' => 'Missing required fields'], 400);
            }

            // Chuyển status về dạng số
            $status = (int) $data['status'];

            // Xử lý theo status
            switch ($status) {
                case 1:
                    \Log::info('SMS thành công', [
                        'smsid' => $data['smsid'],
                        'phone' => $data['phone'] ?? null
                    ]);
                    // TODO: cập nhật trạng thái gửi thành công vào DB
                    break;

                case 2:
                case -11:
                    \Log::info('SMS đang chờ xử lý / chưa có kết quả', [
                        'smsid' => $data['smsid'],
                        'status' => $status,
                        'phone' => $data['phone'] ?? null
                    ]);
                    // TODO: có thể lên lịch kiểm tra lại nếu cần
                    break;

                case 0:
                    \Log::warning('SMS gửi thất bại', [
                        'smsid' => $data['smsid'],
                        'telco' => $data['telco'] ?? null,
                        'error' => $data['error'] ?? null,
                        'phone' => $data['phone'] ?? null
                    ]);
                    // TODO: lưu thất bại để xử lý lại hoặc thống kê
                    break;

                default:
                    \Log::warning('SMS unknown status', [
                        'smsid' => $data['smsid'],
                        'status' => $status
                    ]);
                    break;
            }

            return response()->json(['status' => 1]);
        } catch (\Throwable $e) {
            \Log::error('FPT SMS Callback: Exception occurred', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }
}
