<?php

namespace JamstackVietnam\Core\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use JamstackVietnam\Core\Models\Admin;
use JamstackVietnam\Core\Traits\HasCrudActions;

class AdminController extends Controller
{
    use HasCrudActions;

    public $model = Admin::class;

    public $appends = [
        'index' => [],
        'form' => []
    ];

    private function folder()
    {
        return "@Core/" . Str::studly($this->getTable());
    }

    private function beforeStore($request, $rules)
    {
        $email = $request->input('email');
        $phone = $request->input('phone');
        $id = $request->input('id');

        // Clean up existing soft-deleted records with the same email or phone
        // before validation runs.
        if ($email) {
            $existingEmail = Admin::onlyTrashed()
                ->where('email', $email)
                ->where('id', '<>', $id)
                ->first();
            if ($existingEmail) {
                $timestamp = time();
                $existingEmail->email = $existingEmail->email . '.deleted.' . $timestamp;
                if ($existingEmail->phone) {
                    $existingEmail->phone = $existingEmail->phone . '.deleted.' . $timestamp;
                }
                $existingEmail->saveQuietly();
            }
        }

        if ($phone) {
            $existingPhone = Admin::onlyTrashed()
                ->where('phone', $phone)
                ->where('id', '<>', $id)
                ->first();
            if ($existingPhone) {
                $timestamp = time();
                $existingPhone->email = $existingPhone->email . '.deleted.' . $timestamp;
                $existingPhone->phone = $existingPhone->phone . '.deleted.' . $timestamp;
                $existingPhone->saveQuietly();
            }
        }

        // Manually validate uniqueness of email and phone for active administrators
        // to throw a clean ValidationException (422) instead of a Database 500 error.
        $errors = [];

        if ($email) {
            $activeEmail = Admin::where('email', $email)
                ->where('id', '<>', $id)
                ->first();
            if ($activeEmail) {
                $errors['email'] = 'Email này đã có trong cơ sở dữ liệu.';
            }
        }

        if ($phone) {
            $activePhone = Admin::where('phone', $phone)
                ->where('id', '<>', $id)
                ->first();
            if ($activePhone) {
                $errors['phone'] = 'Trường số điện thoại đã có trong cơ sở dữ liệu.';
            }
        }

        if (!empty($errors)) {
            throw \Illuminate\Validation\ValidationException::withMessages($errors);
        }

        if (!$id) {
            $request->validate([
                'role_id' => 'required',
                'password' => 'required|min:8|max:255|confirmed',
            ]);
        }

        return $rules;
    }

    private function afterForm($item)
    {
        if (!current_admin()->hasRole('Super Admin') && $item->hasRole('Super Admin')) {
            abort(403);
        }

        return $item;
    }

    private function beforeIndex($query)
    {
        $query->orderBy('id', 'DESC');

        return $query;
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'password' => 'nullable|min:8|max:255|confirmed',
        ]);

        $data = [
            'name' => $request->input('name'),
        ];

        if ($password = $request->input('password')) {
            $data['password'] = Hash::make($password);
        }

        auth('admin')->user()->update($data);
    }
}
