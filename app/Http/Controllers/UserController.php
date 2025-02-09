<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    private const VALIDATION_RULE_NAME = 'required|max:100';
    private const VALIDATION_RULE_PASSWORD = 'min:5|max:40';

    public function index(Request $request)
    {
        $filter_active = false;
        $filter = [
            'search' => $request->get('search', ''),
            'status' => $request->get('status', '-1'),
            'type' => $request->get('type', '-1'),
        ];
        $q = User::query();
        if ($filter['status'] != -1) {
            $filter_active = true;
            $q->where('is_active', '=', $filter['status']);
        }
        if ($filter['type'] != -1) {
            $filter_active = true;
            $q->where('is_admin', '=', $filter['type']);
        }
        if (!empty($filter['search'])) {
            $q->where('username', 'like', '%' . $filter['search'] . '%');
            $q->orWhere('fullname', 'like', '%' . $filter['search'] . '%');
        }
        $items = $q->orderBy('fullname', 'asc')->paginate(10);
        return view('pages.user.index', compact('items', 'filter', 'filter_active'));
    }

    public function edit(Request $request, $id = 0)
    {
        $user = (int)$id == 0 ? new User() : User::find($id);

        if (!$user) {
            return redirect('user')->with('warning', 'Pengguna tidak ditemukan.');
        } else if ($user->username == 'admin') {
            return redirect('user')->with('warning', 'Akun <b>' . $user->username . '</b> tidak boleh diubah.');
        }

        if ($request->method() == 'POST') {
            $rules = ['fullname' => self::VALIDATION_RULE_NAME];

            if (!$id) {
                $rules['username'] = 'required|unique:users,username,' . $id . '|min:3|max:40';
            } else if (!empty($request->password)) {
                $rules['password'] = self::VALIDATION_RULE_PASSWORD;
            }

            $data = $request->all();
            $validator = Validator::make($data, $rules);
            if (!$request->validate($rules)) {
                return redirect()->back()->withInput()->withErrors($validator);
            }

            fill_with_default_value($data, ['is_active', 'is_admin'], false);

            if (empty($request->password)) {
                unset($data['password']);
            }

            $user->fill($data);

            if (!$id) {
                $message = 'Akun pengguna <b>' . $data['username'] . '</b> telah dibuat.';
            } else {
                $message = 'Akun pengguna <b>' . $data['username'] . '</b> telah diperbarui.';
            }

            $user->save();

            return redirect('user')->with('info', $message);
        }

        return view('pages.user.edit', compact('user'));
    }

    public function profile(Request $request)
    {
        if (!$user = User::find(Auth::user()->id)) {
            return redirect('login');
        }

        if ($request->method() == 'POST') {
            $changedFields = ['fullname'];
            $rules = [
                'fullname' => self::VALIDATION_RULE_NAME,
            ];

            if (!empty($request->current_password)) {
                $changedFields[] = 'password';
                $rules['current_password'] = 'required';
                $rules['password'] = self::VALIDATION_RULE_PASSWORD . '|confirmed';
                $rules['password_confirmation'] = 'required';
            }

            $validator = Validator::make($request->all(), $rules);

            if (in_array('password', $changedFields)) {
                if (!Hash::check($request->current_password, $user->password)) {
                    $validator->errors()->add('current_password', 'Kata sandi yang anda masukkan anda salah.');
                    return redirect()->back()->withInput()->withErrors($validator);
                }
            }

            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator);
            }

            $user->update($request->only($changedFields));

            return redirect('user/profile')->with('info', 'Profil anda telah diperbarui.');
        }

        return view('pages.user.profile', compact('user'));
    }

    public function delete(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->username == 'admin') {
            return redirect('user')->with('error', 'Akun <b>' . e($user->username) . '</b> tidak boleh dihapus.');
        } else if ($user->id == Auth::user()->id) {
            return redirect('user')->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        if ($request->method() == 'POST') {
            $user->delete();
            $message = 'Akun pengguna <b>' . e($user->username) . '</b> telah dihapus.';
            return redirect('user')->with('info', $message);
        }

        return view('pages.user.delete', compact('user'));
    }
}
