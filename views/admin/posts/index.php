<?php

use app\core\Config;
use app\facades\DB;

extend('admin'); 
section('content');

?>

<form method="get">
    <input type="hidden" name="team_id" value="<?=$_GET['team_id']?>">
    <section class="no-section-sm">
        <div class="no-container-xl">
            <div class="no-admin-content-top">
                <h1 class="no-heading-xs"><?=$title?> 목록</h1>
                <a href="/admin/posts/create?<?=http_build_query($_GET)?>" class="no-btn-primary">
                    <span>게시글 생성하기</span>
                </a>
            </div>
        </div>
    </section>

    <section>
        <div class="no-container-xl">
            <div class="no-admin-box">
                <div class="no-flex-between">
                    <h2 class="no-heading-xxs">검색</h2>
                    <button type="submit" class="no-btn no-btn-black">필터 적용</button>
                </div>
                <div class="no-row no-mg-16--t">
                    <div class="no-col-6 no-col-md-12">
                        <div class="no-form-control">
                            <label for="board_id">게시판</label>
                            <select name="board_id" id="board_id">
                                <option value="">전체</option>
                                <?php 
                                    foreach ($selectedBoards as $board): ?>
                                    <option value="<?=$board['id']?>" <?=($_GET['board_id'] ?? '') == $board['id'] ? 'selected' : ''?>><?=htmlspecialchars($board['name'])?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="no-col-6 no-col-md-12">
                        <div class="no-form-control">
                            <label for="search">제목</label>
                            <input type="text" name="search" id="search" value="<?=$search ?? ''?>">
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <section>
        <div class="no-container-xl">
            <div class="no-admin-box">
                <table class="no-table">
                    <thead>
                        <tr>
                            <th width="8%">번호</th>
                            <th width="*">제목</th>
                            <th width="12%">게시판</th>
                            <th width="12%">조회수</th>
                            <th width="120">생성일</th>
                            <th width="150">관리</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rows as $row): 
                            $board = DB::table('no_board_langs')->where('board_id', '=', $row['board_id'])
                                ->where('locale', '=', Config::get('default_locale'))->first();
                            $lang = DB::table('no_post_langs')
                            ->where('post_id', '=', $row['id'])
                            ->where('locale', '=', Config::get('default_locale'))
                            ->first();

                            $params = $_GET;

                            if($_GET['team_id']) {
                                $params['board_id'] = $row['board_id'];  
                            }

                            $link = '/admin/posts/edit/'.$row['id'].'?'.http_build_query($params);
                        ?>
                        <tr>
                            <td><?= $row['_no'] ?></td>
                            <td>
                                <a href="<?=$link?>" class="no-direct-link">
                                <?= htmlspecialchars($lang['title']) ?>
                                </a>
                            </td>
                            <td><?= $board['name'] ?? '-' ?></td>
                            <td><?= $row['views'] ?? 0?></td>
                            <td><?= formatDate($row['created_at']) ?></td>
                            <td>
                                <menu class="no-admin-action">
                                    <a href="<?=$link?>" class="no-btn-white">수정</a>
                                    <button class="no-btn-error" data-id="<?= $row['id'] ?>">삭제</button>
                                </menu>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="no-mg-16--t"><?= $pagination ?></div>
            </div>
        </div>
    </section>
</form>

<?php endSection(); ?>

<?php section('script'); ?>
<script>
    document.querySelectorAll('[data-id]').forEach(btn => {
        btn.addEventListener('click', async e => {
            if (!confirm('정말로 삭제하시겠습니까?')) return;

            const fd = new FormData();
            fd.set('_method', 'delete');
            const response = await fetch(`/admin/posts/${e.currentTarget.dataset.id}`, {
                method: 'POST',
                body: fd
            });
            const res = await response.json();
            alert(res.message);
            if (res.success) location.reload();
        });
    });
</script>
<?php endSection(); ?>
