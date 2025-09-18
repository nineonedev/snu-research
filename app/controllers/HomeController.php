<?php 

namespace app\controllers;

use app\core\Config;
use app\core\Paginator;
use app\core\Request;
use app\core\Response;
use app\facades\DB;
use app\models\Board;
use app\models\Post;
use app\models\Team;

class HomeController
{
    // 1. 홈
    public function index(Request $request): Response
    {
        return render('home.index');
    }

    // 2. 현대한국종합연구단 - 소개
    public function instituteIntro(Request $request): Response
    {

        return render('home.institute.intro');
    }

    // 2. 현대한국종합연구단 - 인사말
    public function instituteGreeting(Request $request): Response
    {
        return render('home.institute.greeting');
    }

    // 3. 연구팀 목록
    public function teamList(Request $request): Response
    {
        $locale = Config::get('locale');
        $teams = DB::table('no_teams')->get();
        foreach ($teams as &$team) {
            $teamLang = DB::table('no_team_langs')
                ->where('team_id', '=', $team['id'])
                ->where('locale', '=', $locale)
                ->first();
            
           
            $team['path'] = web_path('teams/'.$team['id']);
            $team['name'] = $teamLang['name'] ?? '';
            $team['is_active'] = false; 

        }
        unset($team); 

        return render('home.teams.index', [
            'title' => lang('menu.teams'),
            'sub_title' => lang('sub.teams.desc'),
            'teams' => $teams,
        ]);
    }

    // 3-1. 특정 연구팀 소개 페이지
    public function teamDetail(Request $request, $teamId): Response
    {
        $locale = Config::get('locale');

        $teams = DB::table('no_teams')->get();
        $currentTeam = [];
        foreach ($teams as &$team) {
            $teamLang = DB::table('no_team_langs')
                ->where('team_id', '=', $team['id'])
                ->where('locale', '=', $locale)
                ->first();
            
                
            $team['path'] = web_path('teams/'.$team['id']);
            $team['name'] = $teamLang['name'] ?? '';

            if($teamId == $team['id']) {
                $team['is_active'] = true; 
                $currentTeam  = $team; 
            } else {
                $team['is_active'] = false; 
            }

        }
        unset($team); 

        $title = lang('menu.teams');
        $subTitle = lang('sub.teams.desc');

        $boards = DB::table('no_boards')
            ->where('team_id', '=', $teamId)
            ->get();
		
        $currentPost = [];
        
        foreach ($boards as &$board) {
            $langs = DB::table('no_board_langs')
                ->select(['name'])
                ->where('board_id', '=', $board['id'])
                ->where('locale', '=', $locale)
                ->first();
            $board['name'] = $langs['name'];

            if($board['search_key'] === 'INTRO') {
                $board['is_active'] = true;
                $board['path'] = web_path('teams/'.$teamId);
                
                $post = DB::table('no_posts')
                    ->where('board_id', '=', $board['id'])
                    ->first(); 
                
                $postLang = DB::table('no_post_langs')
                    ->where('post_id', '=', $post['id'])
                    ->where('locale', '=', $locale)
                    ->first();
                
                $currentPost = $post;
                $currentPost['lang'] = $postLang;

            } else {
                $board['is_active'] = false;
                $post = [];
                $board['path'] = web_path('teams/'.$teamId.'/board/'.$board['id']);
            }
        }
        unset($board); 


        return render('home.teams.detail', [
            'title' => $currentTeam['name'],
            'subTitle' => $subTitle,
            'boards' => $boards,
            'post' => $currentPost,
            'teams' => $teams,
            'team' => $currentTeam,
        ]);
    }

    // 3-2. 특정 연구팀의 게시판 보기
    public function teamBoard(Request $request, $teamId, $boardId): Response
    {
        $locale = Config::get('locale');

        // 팀 목록 + 언어명
        $teams = DB::table('no_teams')->get();
        $currentTeam = [];

        foreach ($teams as &$team) {
            $teamLang = DB::table('no_team_langs')
                ->where('team_id', '=', $team['id'])
                ->where('locale', '=', $locale)
                ->first();

            $team['path'] = web_path('teams/' . $team['id']);
            $team['name'] = $teamLang['name'] ?? '';

            if ($team['id'] == $teamId) {
                $team['is_active'] = true;
                $currentTeam = $team;
            } else {
                $team['is_active'] = false;
            }
        }
        unset($team);


        $currentBoard = [];

        $boards = DB::table('no_boards')
            ->where('team_id', '=', $teamId)
            ->get();
            
        foreach ($boards as &$board) {
            $boardLang = DB::table('no_board_langs')
                ->select(['name'])
                ->where('board_id', '=', $board['id'])
                ->where('locale', '=', $locale)
                ->first();
            $board['name'] = $boardLang['name'];
            $board['path'] = web_path('teams/'.$teamId.'/board/'.$board['id']);

            if($board['id'] == $boardId) {
                $board['is_active'] = true; 
                $currentBoard = $board;
            } else {
                $board['is_active'] = false; 
            }

        }
        unset($board); 

		if($currentBoard['search_key'] === 'INTRO') {
			return $this->teamDetail($request, $teamId);
		}

        $search = $request->param('search') ?? '';

        $query = DB::table('no_posts')
            ->leftJoin('no_post_langs', 'no_posts.id', '=', 'no_post_langs.post_id')
            ->select([
                'no_posts.*',
                'no_post_langs.title as lang_title',
                'no_post_langs.content as lang_content'
            ])
            ->where('no_posts.board_id', '=', $boardId)
            ->where('no_post_langs.locale', '=', $locale);

        if ($search) {
            $query->where('no_post_langs.title', 'like', "%{$search}%");
        }

        $paginator = $query->orderBy('no_posts.id', 'desc')
            ->paginate(8, $_GET['page'] ?? 1)
            ->withNumbers();

        $data = $paginator->toArray();

        foreach ($data['data'] as &$post) {
            $post['lang'] = [
                'title' => $post['lang_title'],
                'content' => $post['lang_content'],
            ];
            $post['path'] = web_path("teams/{$teamId}/board/{$boardId}/post/{$post['id']}");
        }
        unset($post);


        return render('home.teams.board', [
            'search' => $search,
            'title' => lang('menu.teams'),
            'subTitle' => lang('sub.teams.desc'),
            'teams' => $teams,
            'team' => $currentTeam,
            'board' => $currentBoard,
            'boards' => $boards,
            'posts' => $data['data'],
            'total' => $data['total'],
            'pagination' => $paginator->render(),
        ]);

    }


    // 3-3. 연구팀 게시판의 게시글 상세
    public function teamPost(Request $request, $teamId, $boardId, $postId): Response
    {
        $locale = Config::get('locale');

        // 팀 목록 + 현재 팀
        $teams = DB::table('no_teams')->get();
        $currentTeam = [];

        foreach ($teams as &$team) {
            $teamLang = DB::table('no_team_langs')
                ->where('team_id', '=', $team['id'])
                ->where('locale', '=', $locale)
                ->first();

            $team['path'] = web_path("teams/{$team['id']}");
            $team['name'] = $teamLang['name'] ?? '';

            $team['is_active'] = $team['id'] == $teamId;
            if ($team['is_active']) {
                $currentTeam = $team;
            }
        }
        unset($team);

        // 게시판 목록 + 현재 게시판
        $boards = DB::table('no_boards')
            ->where('team_id', '=', $teamId)
            ->get();
        $currentBoard = [];

        foreach ($boards as &$board) {
            $boardLang = DB::table('no_board_langs')
                ->where('board_id', '=', $board['id'])
                ->where('locale', '=', $locale)
                ->first();

            $board['name'] = $boardLang['name'] ?? '';
            $board['path'] = web_path("teams/{$teamId}/board/{$board['id']}");
            $board['is_active'] = $board['id'] == $boardId;

            if ($board['is_active']) {
                $currentBoard = $board;
            }
        }
        unset($board);

        // 게시글 + 언어 데이터
        $post = DB::table('no_posts')
            ->where('id', '=', $postId)
            ->where('board_id', '=', $boardId)
            ->first();

        if (!$post) {
            Response::back();
        }

        $postModel = Post::find($post['id']);
        $postModel->views = $postModel->views + 1;
        $postModel->save();

        $postLang = DB::table('no_post_langs')
            ->where('post_id', '=', $post['id'])
            ->where('locale', '=', $locale)
            ->first();

        $post['lang'] = $postLang;
        $post['path'] = web_path("teams/{$teamId}/board/{$boardId}/post/{$post['id']}");

        $listPath = web_path("teams/{$teamId}/board/{$boardId}?",http_build_query($_GET)); 

        return render('home.teams.post', [
            'title' => lang('menu.teams'),
            'subTitle' => lang('sub.teams.desc'),
            'teams' => $teams,
            'team' => $currentTeam,
            'boards' => $boards,
            'board' => $currentBoard,
            'post' => $post,
            'listPath' => $listPath,
        ]);
    }


    // 4. 활동 메인
public function activityList(Request $request): Response
{
    $locale = Config::get('locale');

    // CONF, COLL, PUB 타입의 게시판만 조회
    $searchKeys = ['CONF', 'COLL', 'PUB'];
    $boards = DB::table('no_boards')
        ->where('is_public', '=', 1)
        ->whereNull('team_id')
        ->whereIn('search_key', $searchKeys)
        ->get();

    foreach ($boards as &$board) {
        $lang = DB::table('no_board_langs')
            ->where('board_id', '=', $board['id'])
            ->where('locale', '=', $locale)
            ->first();

        $board['name'] = $lang['name'] ?? '';
        $board['path'] = web_path("activities/{$board['id']}");
    }
    unset($board);

    return render('home.activities.index', [
        'title' => lang('menu.activities'),
        'boards' => $boards,
    ]);
}

    // 4-1. 활동 게시판 보기
    public function activityBoard(Request $request, $boardId): Response
    {
        $locale = Config::get('locale');
        $search = $request->param('search') ?? '';

        $boards = DB::table('no_boards')
            ->where('is_public', '=', 1)
            ->get();
        $currentBoard = [];

        foreach ($boards as &$board) {
            $boardLang = DB::table('no_board_langs')
                ->where('board_id', '=', $board['id'])
                ->where('locale', '=', $locale)
                ->first();

            $board['name'] = $boardLang['name'] ?? '';
            $board['path'] = web_path("activities/{$board['id']}");
            $board['is_active'] = $board['id'] == $boardId;

            if ($board['is_active']) {
                $currentBoard = $board;
            }
        }
        unset($board);

        if (!$currentBoard) {
            // abort(404);
        }

        if($currentBoard['search_key'] === 'NEWS') {
            $title = lang('menu.news');
        } else {
            $title = lang('menu.activities');
        }

        // 검색 포함 게시글 목록
        $query = DB::table('no_posts')
            ->leftJoin('no_post_langs', 'no_posts.id', '=', 'no_post_langs.post_id')
            ->select([
                'no_posts.*',
                'no_post_langs.title as lang_title',
                'no_post_langs.content as lang_content'
            ])
            ->where('no_posts.board_id', '=', $boardId)
            ->where('no_post_langs.locale', '=', $locale);

        if ($search) {
            $query->where('no_post_langs.title', 'like', "%{$search}%");
        }

        $paginator = $query->orderBy('no_posts.id', 'desc')
            ->paginate(8, $_GET['page'] ?? 1)
            ->withNumbers();

        $data = $paginator->toArray();

        foreach ($data['data'] as &$post) {
            $post['lang'] = [
                'title' => $post['lang_title'],
                'content' => $post['lang_content'],
            ];
            $post['path'] = web_path("activities/{$boardId}/post/{$post['id']}");
        }
        unset($post);

		if ($currentBoard['search_key'] === 'NEWS') {
			$boards = []; 
		} else {
			$searchKeys = ['CONF', 'VIDEO', 'COLL', 'PUB'];

			$boards = array_filter($boards, function($item){
				return $item['search_key'] !== 'NEWS';
			});

			usort($boards, function($a, $b) use ($searchKeys) {
				$aIndex = array_search($a['search_key'], $searchKeys);
				$bIndex = array_search($b['search_key'], $searchKeys);

				// 존재하지 않는 키는 맨 뒤로
				$aIndex = $aIndex === false ? PHP_INT_MAX : $aIndex;
				$bIndex = $bIndex === false ? PHP_INT_MAX : $bIndex;

				return $aIndex - $bIndex;
			});
		}


        return render('home.activities.board', [
            'search' => $search,
            'title' => $title,
            'boards' => $boards,
            'board' => $currentBoard,
            'posts' => $data['data'],
            'total' => $data['total'],
            'pagination' => $paginator->render(),
        ]);
    }

    // 4-2. 활동 게시글 상세
    public function activityPost(Request $request, $boardId, $postId): Response
	{
		$locale = Config::get('locale');

		// 1) 전체 공개 보드 로드 및 공통 가공(name, path, is_active)
		$boards = DB::table('no_boards')
			->where('is_public', '=', 1)
			->get();

		$currentBoard = [];

		foreach ($boards as &$board) {
			$boardLang = DB::table('no_board_langs')
				->where('board_id', '=', $board['id'])
				->where('locale', '=', $locale)
				->first();

			$board['name']      = $boardLang['name'] ?? '';
			$board['path']      = web_path("activities/{$board['id']}");
			$board['is_active'] = ($board['id'] == $boardId);

			if ($board['is_active']) {
				$currentBoard = $board;
			}
		}
		unset($board);

		if (!$currentBoard) {
			return Response::back();
		}

		// 2) 타이틀 결정 (NEWS이면 뉴스, 아니면 Activities)
		if ($currentBoard['search_key'] === 'NEWS') {
			$title  = lang('menu.news');
		} else {
			$title  = lang('menu.activities');
		}

		// 3) 리스트 페이지와 동일한 boards 구성 규칙 적용
		if ($currentBoard['search_key'] === 'NEWS') {
			// 뉴스 상세에서는 탭(boards) 숨김
			$boards = [];
		} else {
			// NEWS 탭 제외 + 고정 정렬
			$searchKeys = ['CONF', 'VIDEO', 'COLL', 'PUB'];

			$boards = array_filter($boards, function ($item) {
				return ($item['search_key'] !== 'NEWS');
			});

			usort($boards, function ($a, $b) use ($searchKeys) {
				$aIndex = array_search($a['search_key'], $searchKeys, true);
				$bIndex = array_search($b['search_key'], $searchKeys, true);

				$aIndex = ($aIndex === false) ? PHP_INT_MAX : $aIndex;
				$bIndex = ($bIndex === false) ? PHP_INT_MAX : $bIndex;

				return $aIndex <=> $bIndex;
			});
		}

		// 4) 게시글 조회(+조회수 증가)
		$post = DB::table('no_posts')
			->where('id', '=', $postId)
			->where('board_id', '=', $boardId)
			->first();

		if (!$post) {
			return Response::back();
		}

		$postModel = \app\models\Post::find($post['id']);
		if ($postModel) {
			$postModel->views = $postModel->views + 1;
			$postModel->save();
		}

		// 다국어 로드
		$postLang = DB::table('no_post_langs')
			->where('post_id', '=', $post['id'])
			->where('locale', '=', $locale)
			->first();

		$post['lang'] = $postLang;
		$post['path'] = web_path("activities/{$boardId}/post/{$post['id']}");

		// 목록으로 돌아가기 경로(기존 쿼리 유지)
		$listPath = web_path("activities/{$boardId}?", http_build_query($_GET));

		// 5) 렌더 (board/boards를 activityBoard와 동일한 스키마로 제공)
		return render('home.activities.post', [
			'title'    => $title,
			'board'    => $currentBoard, // ← 단일 현재 보드
			'boards'   => $boards,       // ← 탭(필요 시 정렬/필터 적용)
			'post'     => $post,
			'listPath' => $listPath,
		]);
	}

    public function search(Request $request)
    {
        $locale = Config::get('locale');
        $query = $request->param('query');
        $search = trim($query ?? '');

        $page = (int)($_GET['page'] ?? 1);
        $perPage = 8;
        $offset = ($page - 1) * $perPage;

        if (!$search) {
            return render('home.search', [
                'query' => $search,
                'total' => 0,
                'results' => [],
                'pagination' => '',
            ]);
        }

        // 전체 개수
        $total = DB::table('no_post_langs')
            ->where('locale', '=', $locale)
            ->where('title', 'like', "%{$search}%")
            ->count();

        // 페이징된 결과
        $postLangs = DB::table('no_post_langs')
            ->where('locale', '=', $locale)
            ->where('title', 'like', "%{$search}%")
            ->orderBy('post_id', 'desc')
            ->limit($perPage)
            ->offset($offset)
            ->get();

        $results = [];

        foreach ($postLangs as $postLang) {
            $post = DB::table('no_posts')
                ->where('id', '=', $postLang['post_id'])
                ->first();
            if (!$post) continue;

            $board = DB::table('no_boards')
                ->where('id', '=', $post['board_id'])
                ->first();
            if (!$board) continue;

            $path = '';
            if ($board['team_id']) {
                $path = web_path("teams/{$board['team_id']}/board/{$board['id']}/post/{$post['id']}");
            } elseif ($board['is_public']) {
                $path = web_path("activities/{$board['id']}/post/{$post['id']}");
            }

            $results[] = [
                'image' => $post['image'] ? UPLOAD_URL . $post['image'] : img('default.jpg'),
                'title' => $postLang['title'],
                'path' => $path,
            ];
        }

        // 커스텀 Paginator 생성
        $paginator = (new Paginator($results, $total, $perPage, $page))->withNumbers();

        return render('home.search', [
            'query' => $search,
            'total' => $total,
            'results' => $paginator->toArray()['data'],  // paginate된 결과만 전달
            'pagination' => $paginator->render(),
        ]);
    }



}
