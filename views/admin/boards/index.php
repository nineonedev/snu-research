<?php

use app\core\Config;
use app\facades\DB;
use app\models\Team;
use app\models\TeamLang;

extend('admin'); 
section('content');

$name = $_GET['name'] ?? '';
$team_id = $_GET['team_id'] ?? '';

?>

<form method="get">

    <section class="no-section-sm">
        <div class="no-container-xl">
            <div class="no-admin-content-top">
                <h1 class="no-heading-xs">게시판 목록</h1>
                <a href="/admin/boards/create" class="no-btn-primary">
                    <span>게시판 생성하기</span>
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
                            <label for="team_id">팀</label>
                            <select name="team_id" id="team_id">
                                <option value="">전체</option>
                                <?php foreach (Team::all() as $team): 
                                    $teamLang = DB::table('no_team_langs')
                                        ->where('team_id', '=', $team->id)  
                                        ->where('locale', '=', Config::get('default_locale'))  
                                        ->first();
                                    $selected = $team_id == $team->id ? 'selected' : '';
                                ?>
                                    <option value="<?=$team->id?>" <?=$selected?>><?=$teamLang['name']?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="no-col-6 no-col-md-12">
                        <div class="no-form-control">
                            <label for="name">이름</label>
                            <input type="text" name="name" id="name" value="<?=$name?>">
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
                            <th width="20%">이미지</th>
                            <th width="20%">팀</th>
                            <th width="*">이름</th>
                            <th width="15%">생성일</th>
                            <th width="150">관리</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($rows) : ?>
                            <?php foreach ($rows as $row): 
                                $lang = DB::table('no_board_langs')
                                        ->where('board_id', '=', $row['id'])
                                        ->where('locale', '=', Config::get('default_locale'))
                                        ->first();
                                $params = http_build_query($_GET);
                                $link = "/admin/boards/edit/".$row['id'].'?'.$params;
                                
                                $teamLang = DB::table('no_team_langs')
                                ->where('team_id', '=', $row['team_id'])
                                ->where('locale', '=', Config::get('default_locale'))
                                ->first();
                                
                            ?>
                            <tr>
                                <td><?= $row['_no'] ?></td>
                                <td>
                                    <?php if($row['image']) : ?>
                                    <div class="no-img-table-box">
                                        <img src="<?=UPLOAD_URL . $row['image']?>" alt="">
                                    </div>
                                    <?php else: ?>
                                    -
                                    <?php endif; ?>
                                </td>
                                <td><?= $teamLang ? $teamLang['name'] : '-'?></td>
                                <td>
                                    <a href="<?=$link?>" class="no-direct-link"><?= $lang['name'] ?? '-' ?></a>
                                </td>
                                <td><?= formatDate($row['created_at']) ?></td>
                                <td>
                                    <menu class="no-admin-action">
                                        <a href="<?=$link?>" class="no-btn-white">수정</a>
                                        <button class="no-btn-error" data-id="<?= $row['id'] ?>">삭제</button>
                                    </menu>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

                <div class="no-mg-16--t"><?= $pagination ?></div>
            </div>
        </div>
    </section>
</form>

<?php endSection(); ?>

<?php section('script') ?>

<script>
    const deleteButtons = document.querySelectorAll('[data-id]');

    const handleDelete = async (e) => {
        e.preventDefault();
        if (!confirm('정말로 삭제하시겠습니까?')) return;

        const id = e.currentTarget.dataset.id;
        const fd = new FormData();
        fd.set('_method', 'delete');

        const response = await fetch(`/admin/boards/${id}`, {
            method: 'post',
            body: fd
        });

        const resData = await response.json();
        alert(resData.message);

        if (resData.success) {
            location.reload();
        }
    };

    deleteButtons.forEach(btn => {
        btn.addEventListener('click', handleDelete);
    });
</script>

<?php endSection(); ?>
