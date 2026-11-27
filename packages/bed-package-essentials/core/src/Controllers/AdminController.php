<?php

namespace JamstackVietnam\Core\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use JamstackVietnam\Core\Models\Admin;
use JamstackVietnam\Core\Traits\HasCrudActions;
use Spatie\Permission\Models\Role;

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
        $id = $request->input('id') ?? $request->route('id');

        if (!current_admin()?->hasRole('Super Admin')) {
            if ($id) {
                $targetAdmin = Admin::withTrashed()->find($id);
                if ($targetAdmin && $targetAdmin->hasRole('Super Admin')) {
                    abort(403);
                }
            }

            $submittedRoles = [];
            if ($request->filled('role_id')) {
                $roleIdInput = $request->input('role_id');
                $submittedRoles = array_merge($submittedRoles, is_array($roleIdInput) ? $roleIdInput : [$roleIdInput]);
            }
            if ($request->filled('roles')) {
                $rolesInput = $request->input('roles');
                $submittedRoles = array_merge($submittedRoles, is_array($rolesInput) ? $rolesInput : [$rolesInput]);
            }

            $roleIds = [];
            foreach ($submittedRoles as $submittedRole) {
                if (is_array($submittedRole)) {
                    if (isset($submittedRole['name']) && strcasecmp((string) $submittedRole['name'], 'Super Admin') === 0) {
                        abort(403);
                    }
                    if (isset($submittedRole['id']) && is_numeric($submittedRole['id'])) {
                        $roleIds[] = (int) $submittedRole['id'];
                    }
                } elseif (is_numeric($submittedRole)) {
                    $roleIds[] = (int) $submittedRole;
                } elseif (is_string($submittedRole) && strcasecmp(trim($submittedRole), 'Super Admin') === 0) {
                    abort(403);
                }
            }

            if (!empty($roleIds) && Role::whereIn('id', array_unique($roleIds))->where('name', 'Super Admin')->exists()) {
                abort(403);
            }
        }

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
        if (!current_admin()?->hasRole('Super Admin') && $item instanceof Admin && $item->hasRole('Super Admin')) {
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
