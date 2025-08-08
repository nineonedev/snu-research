<?php

namespace app\controllers;

use app\core\Config;
use app\core\Request;
use app\core\Response;
use app\core\Session;
use app\facades\DB;
use app\models\Admin;
use app\models\Team;
use Exception;

class AdminController
{

    public function dashboard()
    {
        if(Session::get('role_id') == ROLE_OWNER) {
            return Response::redirect('/admin/teams/');
        } else {
            return Response::redirect('/admin/posts/?team_id='.Session::get('team_id'));
        } 

        return render('admin.index'); 
    }

    
    public function index(Request $request)
    {
        $page = (int)($request->input('page') ?? 1);

        $paginator = Admin::query()
            ->paginate(10, $page)
            ->withNumbers();

        return render('admin.admins.index', [
            'rows' => $paginator->toArray()['data'],
            'pagination' => $paginator->render(),
        ]);
    }

    public function create()
    {
        $teams = Team::all();
        $defaultLocale = Config::get('default_locale');

        
        $admins = Admin::all();

        $role_id = $admins && count($admins) > 2 ? ROLE_ADMIN : ROLE_OWNER;



        foreach ($teams as $team) {
            $lang = DB::table('no_team_langs')
                        ->where('team_id', '=', $team->id)
                        ->where('locale', '=', $defaultLocale)
                        ->first();

            $team->lang = $lang;
        }

        return render('admin.admins.create', [
            'teams' => $teams,
            'role_id' => $role_id
        ]);
    }


    public function store(Request $request)
    {
        try {
            $username = $request->input('username');

            // 아이디 중복 검사
            if (Admin::query()->where('username', '=', $username)->exists()) {
                throw new Exception('이미 사용 중인 아이디입니다.');
            }

            $data = $request->only(['role_id', 'name', 'username']);
            $data['team_id'] = $request->input('team_id') ?: null;
            $data['password'] = password_hash($request->input('password'), PASSWORD_BCRYPT);

            $admin = new Admin($data);
            if (!$admin->save()) {
                throw new Exception('저장 실패');
            }

            return Response::json([
                'success' => true,
                'message' => '저장되었습니다.',
                'data' => $admin->getAttributes(),
            ]);
        } catch (Exception $e) {
            return Response::json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }


    public function edit(Request $request, int $id)
    {
        $admin = Admin::find($id);
        if (!$admin) return Response::back('관리자를 찾을 수 없습니다.');
        
        $teams = Team::all();
        $defaultLocale = Config::get('default_locale');

        foreach ($teams as $team) {
            $lang = DB::table('no_team_langs')
                        ->where('team_id', '=', $team->id)
                        ->where('locale', '=', $defaultLocale)
                        ->first();

            $team->lang = $lang;
        }


        return render('admin.admins.edit', [
            'data' => $admin,
            'teams' => $teams,
        ]);
    }


    public function update(Request $request, int $id)
    {
        $admin = Admin::find($id);
        if (!$admin) return Response::back('관리자를 찾을 수 없습니다.');

        try {
            $admin->team_id = $request->input('team_id') ?: null;
            $admin->name = $request->input('name');
            $admin->username = $request->input('username');

            if ($request->input('password')) {
                $admin->password = password_hash($request->input('password'), PASSWORD_BCRYPT);
            }

            if (!$admin->save()) {
                throw new Exception('수정 실패');
            }

            return Response::json(['success' => true, 'message' => '수정되었습니다.']);
        } catch (Exception $e) {
            return Response::json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function destroy(Request $request, int $id)
    {
        $admin = Admin::find($id);
        if (!$admin) return Response::json(['success' => false, 'message' => '관리자를 찾을 수 없습니다.']);

        if (!$admin->delete()) {
            return Response::json(['success' => false, 'message' => '삭제 실패']);
        }

        return Response::json(['success' => true, 'message' => '삭제되었습니다.']);
    }
}
