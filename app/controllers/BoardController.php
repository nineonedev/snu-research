<?php

namespace app\controllers;

use app\core\Config;
use app\core\QueryBuilder;
use app\core\Request;
use app\core\Response;
use app\core\UploadedFile;
use app\facades\DB;
use app\models\Board;
use app\models\BoardLang;
use Exception;

class BoardController
{
    public function index(Request $request)
    {
        $page = (int) ($request->input('page') ?? 1);
        $team_id = $request->input('team_id');
        $name = $request->input('name');
    
        $query = Board::query();
    
        // team_id 필터
        if ($team_id) {
            $query->where('team_id', '=', $team_id);
        }
    
        // name 필터 (lang 테이블 join 필요)
        if ($name) {
            $subQuery = DB::table('no_board_langs')
                ->select([QueryBuilder::rawExpression('1')]) 
                ->whereColumn('no_board_langs.board_id', '=', 'no_boards.id')
                ->where('no_board_langs.locale', '=', Config::get('default_locale'))
                ->where('no_board_langs.name', 'like', '%' . $name . '%');

            $query->whereExists($subQuery);
        }
        
    
        $paginator = $query->orderBy('id', 'desc')->paginate(10, $page)->withNumbers();
    
        return render('admin.boards.index', [
            'rows' => $paginator->toArray()['data'],
            'pagination' => $paginator->render(),
        ]);
    }
    

    public function getExtras(Request $request, int $id)
    {

        $board = DB::table('no_board')->where('id', '=', $id)->first();

        if (!$board) {
            return Response::json(['success' => false, 'message' => '게시판을 찾을 수 없습니다.']);
        }

        $extras = [];
        for ($i = 1; $i <= 10; $i++) {
            $extras["extra{$i}"] = $board["extra{$i}"] ?? null;
        }

        return Response::json(['success' => true, 'extras' => $extras]);
    }

    public function create()
    {
        return render('admin.boards.create');
    }

    public function store(Request $request)
    {
        try {
            $teamId = $request->input('team_id');

            $data = [
                'team_id' => $teamId !== '' ? $teamId : null,
                'skin' => $request->input('skin'),
                'image' => $this->handleImageUpload($request),
                'is_public' => $request->input('is_public', 0), 
                'search_key' => $request->input('search_key')
            ];
            

            for ($i = 1; $i <= 10; $i++) {
                $key = 'extra' . $i;
                $data[$key] = $request->input('extra_' . $i, '');
            }

            $board = new Board($data);
            

            if (!$board->save()) {
                throw new Exception('게시판 생성에 실패했습니다.');
            }

            $this->saveLangs($board->id, $request->input('langs', []));

            return Response::json([
                'success' => true,
                'message' => '게시판이 생성되었습니다.',
                'data' => $board->getAttributes(),
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
        $board = Board::find($id);
        if (!$board) return Response::back('게시판 정보를 찾을 수 없습니다.');

        $langs = BoardLang::query()->where('board_id', '=', $id)->get();
        $board->langs = $langs;

        return render('admin.boards.edit', [
            'data' => $board->getAttributes(),
        ]);
    }

    public function update(Request $request, int $id)
    {
        $board = Board::find($id);
        if (!$board) return Response::back('게시판 정보를 찾을 수 없습니다.');

        try {
            // team_id: 빈 문자열이면 null 처리
            $teamId = $request->input('team_id');
            $board->team_id = $teamId !== '' ? $teamId : null;
            $board->is_public = $request->input('is_public', 0);
            $board->skin = $request->input('skin');
            $board->search_key = $request->input('search_key');

            // extra1 ~ extra10 업데이트
            for ($i = 1; $i <= 10; $i++) {
                $key = 'extra' . $i;
                $board->$key = $request->input('extra_' . $i, '');
            }

            // 이미지 업로드 처리
            $image = $this->handleImageUpload($request, $board->image);
            if ($image !== null) {
                $board->image = $image;
            }

            if ($request->input('delete_image')) {
                UploadedFile::delete($board->image);
                $board->image = '';
            }

            if (!$board->save()) {
                throw new Exception('수정에 실패했습니다.');
            }

            $this->saveLangs($board->id, $request->input('langs', []));

            return Response::json([
                'success' => true,
                'message' => '수정되었습니다.',
                'data' => $board->getAttributes(),
            ]);
        } catch (Exception $e) {
            return Response::json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }



    public function destroy(Request $request, int $id)
    {
        $board = Board::find($id);
        if (!$board) return Response::back('게시판 정보를 찾을 수 없습니다.');

        if (!$board->delete()) {
            return Response::json([
                'success' => false,
                'message' => '삭제에 실패했습니다.'
            ]);
        }

        if ($board->image && !UploadedFile::delete($board->image)) {
            return Response::json([
                'success' => false,
                'message' => '파일 삭제에 실패했습니다.'
            ]);
        }

        return Response::json([
            'success' => true,
            'message' => '정상적으로 삭제되었습니다.'
        ]);
    }

    // ===== HELPER =====

    private function handleImageUpload(Request $request, ?string $existingPath = null): ?string
    {
        $image = $request->file('image');

        if (!($image instanceof UploadedFile) || !$image->hasUploaded()) {
            return null;
        }

        if (!$image->isValid()) {
            throw new \Exception('유효하지 않은 이미지입니다.');
        }

        if (!$image->isAllowedMimeType('image')) {
            throw new \Exception('이미지 파일만 업로드 가능합니다.');
        }

        if ($existingPath) {
            UploadedFile::delete($existingPath);
        }

        return $image->move(UPLOAD_PATH . DS . 'boards');
    }

    private function saveLangs(int $boardId, array $langs = []): void
    {
        foreach ($langs as $locale => $values) {
            if (!isset($values['name']) || trim($values['name']) === '') continue;

            $lang = BoardLang::query()
                ->where('board_id', '=', $boardId)
                ->where('locale', '=', $locale)
                ->first();

            if ($lang) {
                $lang = new BoardLang($lang);
                $lang->name = $values['name'];
                $lang->save();
            } else {
                BoardLang::create([
                    'board_id' => $boardId,
                    'locale' => $locale,
                    'name' => $values['name']
                ]);
            }
        }
    }
}
