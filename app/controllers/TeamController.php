<?php

namespace app\controllers;

use app\core\Config;
use app\core\Request;
use app\core\Response;
use app\core\UploadedFile;
use app\models\Board;
use app\models\BoardLang;
use app\models\Team;
use app\models\TeamLang;
use Exception;

class TeamController
{
    public function index(Request $request)
    {
        $page = (int) ($request->input('page') ?? 1);
        $paginator = Team::paginate(10, $page)->withNumbers();

        return render('admin.teams.index', [
            
            'rows' => $paginator->toArray()['data'],
            'pagination' => $paginator->render()
        ]);
    }

    public function create()
    {
        return render('admin.teams.create');
    }

    public function store(Request $request)
    {

        try {
            $team = new Team([
                'is_hidden' => $request->input('is_hidden', 0),
                'image' => $this->handleImageUpload($request)
            ]);
            
            if (!$team->save()) {
                throw new Exception('생성에 실패하였습니다.');
            }
            
            $boards = Config::get('team_boards');

            foreach ($boards as $boardConfig) {
                $board = new Board([
                    'team_id' => $team->id,
                    'skin' => $boardConfig['skin'],
                    'search_key' => $boardConfig['search_key']
                ]);

                if (!$board->save()) {
                    throw new Exception('게시판 생성에 실패했습니다.');
                }

                // 게시판 언어 데이터 저장
                foreach ($boardConfig['langs'] as $locale => $langData) {
                    BoardLang::create([
                        'board_id' => $board->id,
                        'locale' => $locale,
                        'name' => $langData['name'],
                    ]);
                }
            }

            $this->saveLangs($team->id, $request->input('langs', []));

            return Response::json([
                'success' => true,
                'message' => '정상적으로 생성되었습니다.',
                'data' => $team->getAttributes(),
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
        $team = Team::find($id);
        if (!$team) return Response::back('정보를 찾을 수 없습니다.');
    
        // 팀 언어 정보
        $langs = TeamLang::query()
            ->where('team_id', '=', $id)
            ->get();
        $team->langs = $langs;
    
        // 해당 팀의 게시판들
        $boards = Board::query()
            ->where('team_id', '=', $id)
            ->get();
    
        // 각 게시판에 대해 언어 정보 붙이기
        foreach ($boards as &$board) {
            $board = new Board($board); 
            $boardLangs = BoardLang::query()
                ->where('board_id', '=', $board->id)
                ->get();
    
            $board->langs = $boardLangs;
        }
    
        return render('admin.teams.edit', [
            'data' => $team->getAttributes(),
            'boards' => $boards,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $team = Team::find($id);
        if (!$team) return Response::back('정보를 찾을 수 없습니다.');

        try {
            $team->is_hidden = $request->input('is_hidden', 0);

            if ($created = $request->input('created_at')) {
                $team->created_at = $this->validateDate($created);
            }

            $image = $this->handleImageUpload($request, $team->image);
            if ($image !== null) $team->image = $image;

            if ($request->input('delete_image')) {
                UploadedFile::delete($team->image);
                $team->image = '';
            }

            if (!$team->save()) {
                throw new Exception('수정에 실패하였습니다.');
            }

            $this->saveLangs($team->id, $request->input('langs', []));

            return Response::json([
                'success' => true,
                'message' => '정상적으로 수정되었습니다.',
                'data' => $team->getAttributes(),
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
        $team = Team::find($id);
        if (!$team) return Response::back('정보를 찾을 수 없습니다.');

        if (!$team->delete()) {
            return Response::json([
                'success' => false,
                'message' => '삭제에 실패하였습니다.'
            ]);
        }

        if ($team->image && !UploadedFile::delete($team->image)) {
            return Response::json([
                'success' => false,
                'message' => '파일 삭제에 실패하였습니다.'
            ]);
        }

        return Response::json([
            'success' => true,
            'message' => '정상적으로 삭제되었습니다.'
        ]);
    }

    // ========== PRIVATE HELPERS ==========

    private function handleImageUpload(Request $request, ?string $existingPath = null): ?string
    {
        $image = $request->file('image');

        if (!($image instanceof UploadedFile) || !$image->hasUploaded()) {
            return null;
        }

        if (!$image->isValid()) {
            throw new \Exception('파일이 유효하지 않습니다.');
        }

        if (!$image->isAllowedMimeType('image')) {
            throw new \Exception('이미지 파일만 업로드 가능합니다.');
        }

        if ($existingPath) {
            UploadedFile::delete($existingPath);
        }

        return $image->move(UPLOAD_PATH . DS . 'teams');
    }


    private function validateDate(string $value): string
    {
        // HTML datetime-local 형식 대응: Y-m-d\TH:i
        $value = str_replace('T', ' ', $value);

        $dt = \DateTime::createFromFormat('Y-m-d H:i', $value) ?: \DateTime::createFromFormat('Y-m-d H:i:s', $value);
        $errors = \DateTime::getLastErrors();

        if ($dt === false || $errors['warning_count'] > 0 || $errors['error_count'] > 0) {
            throw new Exception('날짜 형식이 올바르지 않습니다. 형식은 YYYY-MM-DD HH:MM 또는 HH:MM:SS 이어야 합니다.');
        }

        return $dt->format('Y-m-d H:i:s'); // DB 저장용으로 표준화
    }


    private function saveLangs(int $teamId, array $langs = []): void
    {
        foreach ($langs as $locale => $values) {
            if (!isset($values['name']) || trim($values['name']) === '') continue;

            $lang = TeamLang::query()
                ->where('team_id', '=', $teamId)
                ->where('locale', '=', $locale)
                ->first();

            if ($lang) {
                $lang = new TeamLang($lang);
                $lang->name = $values['name'];
                $lang->save();
            } else {
                TeamLang::create([
                    'team_id' => $teamId,
                    'locale' => $locale,
                    'name' => $values['name']
                ]);
            }
        }
    }
}
