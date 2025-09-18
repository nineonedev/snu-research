<?php

use app\facades\DB;
use app\core\Config;

extend('admin'); 
section('content');
?>

<section class="no-section-sm">
    <div class="no-container-xl">
        <div class="no-admin-content-top">
            <h1 class="no-heading-xs">배너 목록</h1>
            <a href="/admin/banners/create" class="no-btn-primary">
                <span>배너 생성</span>
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
                        <th width="8%">번호</th>
                        <th width="140">이미지</th>
                        <th width="*">제목</th>
                        <th width="12%">타입</th>
                        <th width="12%">순서</th>
                        <th width="12%">상태</th>
                        <th width="150">관리</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $row): 
                        $lang = DB::table('no_banner_langs')
                            ->where('banner_id', '=', $row['id'])
                            ->where('locale', '=', Config::get('default_locale'))
                            ->first();    
                    ?>
                    <tr>
                        <td><?= $row['_no'] ?></td>
                        <td>
                            <?php if ($row['image']) : ?>
                                <div class="no-form-image no-mg-8--t">
                                    <div class="no-form-image-box">
                                        <img src="<?= UPLOAD_URL . $row['image'] ?>" width="120">
                                    </div>
                                </div>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="/admin/banners/edit/<?=$row['id']?>" class="no-direct-link">
                                <?= $lang ? $lang['title'] : ''?>
                            </a>
                        </td>
                        <td><?= $row['type'] ?></td>
                        <td><?= $row['display_order'] ?></td>
                        <td><?= $row['is_hidden'] ? '숨김' : '노출' ?></td>
                        <td>
                            <menu class="no-admin-action">
                                <a href="/admin/banners/edit/<?= $row['id'] ?>" class="no-btn-white">수정</a>
                                <button type="button" class="no-btn-error" data-id="<?= $row['id'] ?>">삭제</button>
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
    const deleteButtons = document.querySelectorAll('[data-id]');

    deleteButtons.forEach(btn => {
        btn.addEventListener('click', async (e) => {
            e.preventDefault();
            if (!confirm('정말 삭제하시겠습니까?')) return;

            const id = e.currentTarget.dataset.id;
            const fd = new FormData();
            fd.set('_method', 'delete');

            const response = await fetch(`/admin/banners/${id}`, {
                method: 'post',
                body: fd
            });

            const res = await response.json();
            alert(res.message);
            if (res.success) {
                location.reload();
            }
        });
    });
</script>
<?php endSection(); ?>
