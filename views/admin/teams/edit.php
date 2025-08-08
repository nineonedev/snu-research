<?php

use app\core\Config;

extend('admin'); 
section('content');

$locales = Config::get('locales');

?>

<section class="no-section-sm">
    <div class="no-container-md">
        <div class="no-admin-content-top">
            <h1 class="no-heading-xs">연구팀 수정</h1>
        </div>
    </div>
</section>

<!-- 연구팀 기본 정보 -->
<form method="post" enctype="multipart/form-data" id="frm">
    <section class="no-section-sm--b">
        <div class="no-container-md">
            <div class="no-admin-box">
                <?= csrf_token() ?>
                <input type="hidden" name="id" value="<?= $data['id'] ?>">

                <div class="no-form-grid no-mg-12--b">
                    <?php foreach ($data['langs'] as $lang): ?>
                        <div class="no-form-control">
                            <label for="name_<?= $lang['locale'] ?>">이름 (<?= strtoupper($locales[$lang['locale']]) ?>)</label>
                            <input type="text" name="langs[<?= $lang['locale'] ?>][name]" id="name_<?= $lang['locale'] ?>" value="<?= $lang['name'] ?>">
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="no-form-control">
                    <label for="image">이미지</label>
                    <input type="file" name="image" id="image">

                    <?php if ($data['image']) : ?>
                        <div class="no-form-image no-mg-8--t">
                            <div class="no-form-image-box">
                                <img src="<?=UPLOAD_URL . DS . $data['image']?>" alt="">
                            </div>
                            <div class="no-mg-4--t">
                                <div class="no-form-check">
                                    <label for="delete_image">
                                        <input type="checkbox" name="delete_image" id="delete_image" value="1">
                                        <div class="no-form-check-box">
                                            <i class="fa-regular fa-check"></i>
                                        </div>
                                        <span class="no-form-check-text">이미지 삭제</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="no-form-control">
                    <label>활성 여부</label>
                    <div class="no-form-switch">
                        <label for="is_hidden">
                            <input type="checkbox" name="is_hidden" id="is_hidden" value="1" <?= $data['is_hidden'] ? 'checked' : '' ?>>
                            <div class="no-form-switch-box">
                                <span class="no-form-switch-knob"></span>
                            </div>
                            <span class="no-form-switch-text">화면에서 숨김</span>
                        </label>
                    </div>
                </div>

                <div class="no-form-control">
                    <label for="created_at">생성일</label>
                    <input type="datetime-local" name="created_at" id="created_at"
                        value="<?= date('Y-m-d\TH:i', strtotime($data['created_at'] ?? date('Y-m-d H:i:s'))) ?>">
                </div>
            </div>
        </div>
    </section>


    <div class="no-section-lg--b">
        <div class="no-container-md">
            <menu class="no-admin-action">
                <a href="/admin/teams" class="no-btn-white"><span>취소</span></a>
                <button type="submit" class="no-btn-primary" data-method="patch"><span>수정</span></button>
                <button type="submit" class="no-btn-error" data-method="delete"><span>삭제</span></button>
            </menu>
        </div>
    </div>
</form>

<?php endSection() ?>

<?php section('script') ?>
<script>
    const form = document.getElementById('frm');

    const handleSubmit = async (e) => {
        e.preventDefault();

        const fd = new FormData(form);
        const method = e.submitter.dataset.method;

        if (!method) return;

        if (method === 'delete') {
            const confirmed = confirm('정말로 삭제하시겠습니까?');
            if (!confirmed) return;
        }

        fd.set('_method', method);

        const response = await fetch(`/admin/teams/${fd.get('id')}`, {
            method: 'post',
            body: fd
        });

        const res = await response.json();
        alert(res.message);

        if (res.success) {
            if (method === 'delete') {
                window.location.href = '/admin/teams';
            } else {
                location.reload();
            }
        }
    };

    form.addEventListener('submit', handleSubmit);

    
</script>
<?php endSection() ?>
