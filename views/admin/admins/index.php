<?php

use app\core\Config;
use app\core\Session;
use app\facades\DB;
use app\models\Team;
use app\models\TeamLang;

extend('admin');
section('content');

?>

<section class="no-section-sm">
    <div class="no-container-xl">
        <div class="no-admin-content-top">
            <h1 class="no-heading-xs">관리자 목록</h1>
            <a href="/admin/admins/create" class="no-btn-primary">
                <span>관리자 생성</span>
            </a>
        </div>
    </div>
</section>

<section>
    <div class="no-container-xl">
        <div class="no-admin-box">
            <table class="no-table">
                <thead>
                    <tr>
                        <th>번호</th>
                        <th>팀</th>
                        <th>이름</th>
                        <th>아이디</th>
                        <th>관리</th>
                    </tr>
                </thead>
                <tbody>

                    <?php foreach ($rows as $row): 
                        if ($row['role_id'] == ROLE_OWNER) continue; 
                        $team = Team::find($row['team_id']);
                        $teamLang = DB::table('no_team_langs')
                            ->where('team_id', '=', $team->id)
                            ->where('locale', '=', Config::get('default_locale'))
                            ->first();
                    ?>
                        <tr>
                            <td><?= $row['_no'] ?></td>
                            <td><?= $teamLang ? $teamLang['name']: ''?></td>
                            <td>
                                <a href="/admin/admins/edit/<?=$row['id']?>" class="no-direct-link"><?= htmlspecialchars($row['name']) ?></a>
                            </td>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td>
                                <menu class="no-admin-action">
                                    <a href="/admin/admins/edit/<?= $row['id'] ?>" class="no-btn-white">수정</a>
                                    <?php if(Session::get('role_id') == ROLE_OWNER) :?>
                                    <button class="no-btn-error" data-id="<?= $row['id'] ?>">삭제</button>
                                    <?php endif;?>
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

<?php endSection(); ?>

<?php section('script'); ?>
<script>
    document.querySelectorAll('[data-id]').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            if (!confirm('정말 삭제하시겠습니까?')) return;
            const id = e.currentTarget.dataset.id;
            const fd = new FormData();
            fd.set('_method', 'delete');

            const res = await fetch(`/admin/admins/${id}`, {
                method: 'POST',
                body: fd
            });

            const result = await res.json();
            alert(result.message);
            if (result.success) {
                location.reload();
            }
        });
    });
</script>
<?php endSection(); ?>
