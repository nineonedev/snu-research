<?php

namespace app\controllers;

use app\core\Config;
use app\core\Request;
use app\core\Response;
use app\core\UploadedFile;
use app\facades\DB;
use app\models\Post;
use app\models\PostLang;
use app\models\Board;
use Exception;

class PostController
{
    public function index(Request $request)
    {
        $search = $_GET['search'] ?? ''; 
        $page = (int)($request->input('page') ?? 1);
        $query = Post::query()->orderByDesc('id');

        $teamId = $request->input('team_id');
        $boardId = $request->input('board_id');
        $isPublic = $request->input('is_public');

        $title = '전체 게시판'; 
        $selectedBoards = [];

        if ($teamId) {
            $boardIds = Board::query()
                ->where('team_id', '=', $teamId)
                ->pluck('id');
            
            foreach ($boardIds as $id) {
                $lang = DB::table('no_board_langs')
                ->where('board_id', '=', $id)
                ->where('locale', '=', Config::get('default_locale'))
                ->first();

                $selectedBoards[] = [
                    'id' => $id,
                    'name' =>  $lang['name']
                ];
            }


            if ($boardId && in_array($boardId, $boardIds)) {
                $query->where('board_id', '=', $boardId);
            } else {
                $query->whereIn('board_id', $boardIds);
            }

            if ($search) {
                $postIds = DB::table('no_post_langs')
                    ->where('locale', '=', Config::get('default_locale'))
                    ->where('title', 'like', '%' . $search . '%')
                    ->pluck('post_id');
                
                $query->whereIn('id', $postIds);
            }

            $teamLang = DB::table('no_team_langs')
                ->where('team_id', '=', $teamId)
                ->where('locale', '=', Config::get('default_locale'))
                ->first();

            $title = $teamLang['name'] ?? '연구팀 게시판';
        } elseif ($boardId && $isPublic) {
            $query->where('board_id', '=', $boardId);

            $boardLang = DB::table('no_board_langs')
                ->where('board_id', '=', $boardId)
                ->where('locale', '=', Config::get('default_locale'))
                ->first();

            $title = $boardLang['name'] ?? '공용 게시판';
        }

        $paginator = $query->paginate(10, $page)->withNumbers();

        return render('admin.posts.index', [
            'selectedBoards' => $selectedBoards,
            'rows' => $paginator->toArray()['data'],
            'pagination' => $paginator->render(),
            'title' => $title,
            'search' => $search
        ]);
    }

    

    public function create(Request $request)
    {
        $teamId = $request->input('team_id');
        $boardId = $request->input('board_id');
        $isPublic = $request->input('is_public');
        $defaultLocale = Config::get('default_locale');

        $pageTitle = '게시글 생성';

        if ($teamId) {
            $teamLang = DB::table('no_team_langs')
                ->where('team_id', '=', $teamId)
                ->where('locale', '=', $defaultLocale)
                ->first();

            $pageTitle = ($teamLang['name'] ?? '연구팀') . ' 게시글 생성';

        } elseif ($isPublic && $boardId) {
            $boardLang = DB::table('no_board_langs')
                ->where('board_id', '=', $boardId)
                ->where('locale', '=', $defaultLocale)
                ->first();

            $pageTitle = ($boardLang['name'] ?? '공용 게시판') . ' 게시글 생성';
        }

        return render('admin.posts.create', [
            'pageTitle' => $pageTitle
        ]);
    }


    public function store(Request $request)
    {
        try {
            $boardId = $request->input('board_id');
            $board = Board::find($boardId);
            if (!$board) throw new Exception('게시판을 선택해주세요.');

            $imagePath = null;
            $imageFile = $request->file('image');
            if ($imageFile instanceof UploadedFile && $imageFile->hasUploaded()) {
                if (!$imageFile->isValid() || !$imageFile->isAllowedMimeType('image')) {
                    throw new Exception('대표 이미지가 유효하지 않습니다.');
                }
                $imagePath = $imageFile->move(UPLOAD_PATH . DS . 'posts');
            }

            $post = new Post([
                'board_id' => $boardId,
                'is_hidden' => $request->input('is_hidden', 0),
                'is_notice' => $request->input('is_notice', 0),
                'link_url' => $request->input('link_url'),
                'image' => $imagePath,
            ]);

            if (!$post->save()) {
                throw new Exception('게시글 저장 실패');
            }

            $langs = $request->input('langs', []);
            foreach ($langs as $locale => $values) {
                $images = [];

                for ($i = 1; $i <= 10; $i++) {
                    $inputName = "image_{$locale}_{$i}";
                    $file = $request->file($inputName);

                    if ($file instanceof UploadedFile && $file->hasUploaded()) {
                        if (!$file->isValid() || !$file->isAllowedMimeType('image')) {
                            throw new Exception("{$locale}의 image{$i}는 유효하지 않습니다.");
                        }
                        $images["image{$i}"] = $file->move(UPLOAD_PATH . DS . 'posts');
                    }
                }

                PostLang::create(array_merge([
                    'post_id' => $post->id,
                    'locale' => $locale,
                ], $values, $images));
            }

            return Response::json([
                'success' => true,
                'message' => '게시글이 생성되었습니다.',
                'data' => $post->getAttributes(),
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
        $post = Post::find($id);
        if (!$post) return Response::back('게시글을 찾을 수 없습니다.');

        $board = Board::find($post->board_id);
        $langs = PostLang::query()->where('post_id', '=', $id)->get();

        $langMap = [];
        foreach ($langs as $lang) {
            $langMap[$lang['locale']] = $lang;
        }

        $defaultLocale = Config::get('default_locale');
        $pageTitle = null;

        if ($board && $board->team_id) {
            $teamLang = DB::table('no_team_langs')
                ->where('team_id', '=', $board->team_id)
                ->where('locale', '=', $defaultLocale)
                ->first();
            $pageTitle = $teamLang['name'] ?? '연구팀 게시판';
        } elseif ($board && $board->is_public) {
            $boardLang = DB::table('no_board_langs')
                ->where('board_id', '=', $board->id)
                ->where('locale', '=', $defaultLocale)
                ->first();
            $pageTitle = $boardLang['name'] ?? '공용 게시판';
        }

        return render('admin.posts.edit', [
            'data'      => $post->getAttributes(),
            'langs'     => $langMap,
            'board'     => $board->getAttributes(),
            'pageTitle' => $pageTitle . ' 게시글 수정',
        ]);
    }

    public function update(Request $request, int $id)
    {
        $post = Post::find($id);
        if (!$post) return Response::back('게시글을 찾을 수 없습니다.');

        try {
            $isHidden = $request->input('is_hidden', 0);
            $isNotice = $request->input('is_notice', 0);
            $boardId = $request->input('board_id', null); 
            $linkUrl = $request->input('link_url', null);
            $hasPostChanged = false;

            if(!$boardId){
                throw new Exception('게시판을 선택해주세요.');
            }

            if($post->link_url != $linkUrl) {
                $post->link_url = $linkUrl; 
                $hasPostChanged = true; 
            }

            if($post->is_notice != $isNotice) {
                $post->is_notice = $isNotice; 
                $hasPostChanged = true; 
            }

            if($post->is_hidden != $isHidden) {
                $post->is_hidden = $isHidden; 
                $hasPostChanged = true; 
            }

            if($post->board_id !== $boardId) {
                $post->board_id = $boardId; 
                $hasPostChanged = true;
            }
            

            // 대표 이미지 업로드
            $file = $request->file('image');
            if ($file instanceof UploadedFile && $file->hasUploaded()) {
                if (!$file->isValid() || !$file->isAllowedMimeType('image')) {
                    throw new Exception('대표 이미지가 유효하지 않습니다.');
                }

                if (!empty($post->image)) {
                    UploadedFile::delete($post->image);
                }

                $post->image = $file->move(UPLOAD_PATH . DS . 'posts');
                $hasPostChanged = true;
            }

            // 대표 이미지 삭제 체크
            if (isset($_POST['delete_image']) && $_POST['delete_image']) {
                UploadedFile::delete($post->image);
                $post->image = '';
                $hasPostChanged = true;
            }


            $postSaved = $hasPostChanged ? $post->save() : false;

            $langs = $request->input('langs', []);
            $hasLangChanged = false;

            foreach ($langs as $locale => $values) {
                $lang = PostLang::query()
                    ->where('post_id', '=', $post->id)
                    ->where('locale', '=', $locale)
                    ->first();

                if (!$lang) {
                    $lang = new PostLang([
                        'post_id' => $post->id,
                        'locale' => $locale,
                    ]);
                    $hasLangChanged = true;
                } else {
                    $lang = new PostLang($lang);
                }

                $modified = false;

                foreach ($values as $k => $v) {
                    if ($lang->$k !== $v) {
                        $lang->$k = $v;
                        $modified = true;
                    }
                }

                for ($i = 1; $i <= 10; $i++) {
                    $key = "image{$i}";
                    $fileInputName = "image_{$locale}_{$i}";
                    $deleteInputName = "delete_image_{$locale}_{$i}";

                    $file = $request->file($fileInputName);

                    if ($file instanceof UploadedFile && $file->hasUploaded()) {
                        if (!$file->isValid() || !$file->isAllowedMimeType('image')) {
                            throw new Exception("{$locale}의 {$key}는 유효하지 않습니다.");
                        }
                        $lang->$key = $file->move(UPLOAD_PATH . DS . 'posts');
                        $modified = true;
                    }

                    if (isset($_POST[$deleteInputName]) && $_POST[$deleteInputName]) {
                        UploadedFile::delete($_POST[$deleteInputName]);
                        if (!empty($lang->$key)) {
                            $lang->$key = '';
                            $modified = true;
                        }
                    }
                }

                if ($modified) {
                    $lang->save();
                    $hasLangChanged = true;
                }
            }
    
            if (!$postSaved && !$hasLangChanged) {
                throw new Exception('수정할 내용이 없습니다.');
            }

            return Response::json([
                'success' => true,
                'message' => '수정되었습니다.',
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
        $post = Post::find($id);
        if (!$post) return Response::back('게시글을 찾을 수 없습니다.');

        // 삭제 전 관련 이미지 삭제
        $postLangs = PostLang::query()->where('post_id', '=', $id)->get();

        foreach ($postLangs as $lang) {
            // 1. Summernote 내용 안 이미지 경로 추출
            if (!empty($lang['content'])) {
                preg_match_all('/src="([^"]*\/uploads\/summernote\/[^"]+)"/', $lang['content'], $matches);

                foreach ($matches[1] as $imageUrl) {
                    $parsedUrl = parse_url($imageUrl, PHP_URL_PATH); // e.g. /storage/uploads/summernote/abc.png
                    $relativePath = ltrim(str_replace('/storage/uploads/', '', $parsedUrl), '/'); // summernote/abc.png
                    UploadedFile::delete($relativePath);
                }
            }

            // 2. image1~10 삭제 (PostLang 안의 이미지들)
            for ($i = 1; $i <= 10; $i++) {
                $imgKey = "image{$i}";
                if (!empty($lang[$imgKey])) {
                    UploadedFile::delete($lang[$imgKey]);
                }
            }
        }

        // 3. post 자체에 있는 단일 이미지 삭제 (필드명이 있다면)
        if (!empty($post->image)) {
            UploadedFile::delete($post->image);
        }

        // 4. 최종 삭제
        if (!$post->delete()) {
            return Response::json(['success' => false, 'message' => '삭제 실패']);
        }

        return Response::json([
            'success' => true,
            'message' => '삭제되었습니다.',
        ]);
    }

}
