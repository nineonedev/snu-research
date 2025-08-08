<?php 

namespace app\controllers;

use app\core\Request;
use app\core\Response;
use app\core\Session;
use app\facades\DB;
use app\models\Admin;

class AuthController
{
    public function signin()
    {
        if(Session::get('user_id')) {
            return Response::redirect('/admin/');
        }

        return render('admin.auth.signin');
    }

    public function signup()
    {
        $ownerCount = DB::table('no_admins')->where('role_id', '=', ROLE_OWNER)->count();
        
        if($ownerCount >= 2) {
            Response::redirect('/auth/signin');
        }

        return render('admin.auth.signup', [
            'role_id' => $ownerCount >= 2 ? ROLE_ADMIN : ROLE_OWNER
        ]);
    }

    public function logout()
    {
        Session::forget('user_id');
        Session::forget('user_name');
        Session::forget('user_username');

        return Response::alert('정상적으로 로그아웃되었습니다.', '/auth/signin');
    }

    public function login(Request $request)
    {
        $username = $request->input('username');
        $password = $request->input('password');

        if (!$username || !$password) {
            return Response::json([
                'success' => false,
                'message' => '입력필드가 누락되었습니다.'
            ]);
        }

        $admin = Admin::first('username', '=', $username);

        if (!$admin || !password_verify($password, $admin->password)) {
            return Response::json([
                'success' => false,
                'message' => '아이디 또는 패스워드가 일치하지 않습니다.'
            ]);
        }

        Session::set('team_id', $admin->team_id);
        Session::set('role_id', $admin->role_id);
        Session::set('user_id', $admin->id);
        Session::set('user_name', $admin->name);
        Session::set('user_username', $admin->username);

        return Response::json([
            'success' => true,
            'message' => '성공적으로 로그인되었습니다.'
        ]);
    }

    public function register(Request $request)
    {
        $name = $request->input('name');
        $username = $request->input('username');
        $password = $request->input('password');
        $role_id = $request->input('role_id');

        if (!$name || !$username || !$password) {
            return Response::json([
                'success' => false,
                'message' => '입력필드가 누락되었습니다.'
            ]);
        }

        $exists = Admin::exists('username', '=', $username);

        if ($exists) {
            return Response::json([
                'success' => false,
                'message' => '이미 존재하는 아이디입니다.'
            ]);
        }

        $admin = Admin::create([
            'role_id' => $role_id,
            'name' => $name,
            'username' => $username,
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        if ($admin) {
            return Response::json([
                'success' => true,
                'message' => '정상적으로 가입되었습니다.',
                'data' => $admin->getAttributes(),
            ]);
        }

        return Response::json([
            'success' => false,
            'message' => '처리 중 문제가 발생하였습니다.'
        ]);
    }
}
