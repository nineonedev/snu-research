<?php

use app\core\Config;

extend('admin');
section('content');

$locales = Config::get('locales');
$defaultLocale = Config::get('default_locale');
?>

<section class="no-section-sm">
    <div class="no-container-md">
        <div class="no-admin-content-top">
            <h1 class="no-heading-xs">배너 수정</h1>
        </div>
    </div>
</section>

<form method="post" enctype="multipart/form-data" id="frm">
    <?= csrf_token() ?>
    <input type="hidden" name="id" value="<?= $data['id'] ?>">

    <section class="no-section-sm--b">
        <div class="no-container-md">
            <div class="no-admin-box no-mg-16--b">
                <div class="no-tab-menu no-mg-20--b" data-tab-menu="locale-tabs">
                    <?php foreach ($locales as $locale => $label): ?>
                        <button type="button" class="no-chip <?= $locale === $defaultLocale ? 'active' : '' ?>">
                            <?= strtoupper($label) ?>
                        </button>
                    <?php endforeach; ?>
                </div>

                <div id="locale-tabs">
                    <?php foreach ($locales as $locale => $label):
                        $lang = array_filter($data['langs'] ?? [], fn($l) => $l['locale'] === $locale);
                        $lang = array_shift($lang) ?? [];
                    ?>
                    <div class="no-tab-section <?= $locale === $defaultLocale ? 'active' : '' ?>">
                        <div class="no-form-control">
                            <label for="title_<?= $locale ?>">제목 (<?= strtoupper($label) ?>)</label>
                            <input type="text" name="langs[<?= $locale ?>][title]" id="title_<?= $locale ?>" value="<?= htmlspecialchars($lang['title'] ?? '') ?>">
                        </div>
                        <div class="no-form-control">
                            <label for="description_<?= $locale ?>">설명</label>
                            <input type="text" name="langs[<?= $locale ?>][description]" id="description_<?= $locale ?>" value="<?= htmlspecialchars($lang['description'] ?? '') ?>">
                        </div>
                        <div class="no-form-control">
                            <label for="link_<?= $locale ?>">링크</label>
                            <input type="text" name="langs[<?= $locale ?>][link]" id="link_<?= $locale ?>" value="<?= htmlspecialchars($lang['link'] ?? '') ?>">
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="no-admin-box">
                <div class="no-form-control">
                    <label for="type">타입</label>
                    <select name="type" id="type">
                        <?php foreach (Config::get('banner_types') as $key => $label): ?>
                            <option value="<?=$key?>" <?= $data['type'] == $key ? 'selected' : '' ?>><?=$label?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="no-form-control">
                    <label for="image">이미지</label>
                    <input type="file" name="image" id="image">
                    <?php if ($data['image']) : ?>
                        <div class="no-form-image no-mg-8--t">
                            <div class="no-form-image-box">
                                <img src="<?= UPLOAD_URL . '/' . $data['image'] ?>" alt="">
                            </div>
                            <div class="no-mg-4--t">
                                <div class="no-form-check">
                                    <label>
                                        <input type="checkbox" name="delete_image" value="1">
                                        <div class="no-form-check-box"><i class="fa-regular fa-check"></i></div>
                                        <span class="no-form-check-text">이미지 삭제</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="no-form-control">
                    <label for="display_order">정렬순서</label>
                    <input type="number" name="display_order" id="display_order" value="<?= $data['display_order'] ?>">
                </div>

                <div class="no-col-6 no-col-md-12">
                    <div class="no-form-label">노출 여부</div>
                    <div class="no-form-switch">
                        <label for="is_hidden">
                            <input type="checkbox" name="is_hidden" id="is_hidden" value="1" <?= $data['is_hidden'] ? 'checked' : '' ?>>
                            <div class="no-form-switch-box"><span class="no-form-switch-knob"></span></div>
                            <span class="no-form-switch-text">배너를 숨깁니다.</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="no-section-lg--b">
        <div class="no-container-md">
            <menu class="no-admin-action">
                <a href="/admin/banners" class="no-btn-white">취소</a>
                <button type="submit" class="no-btn-primary" data-method="patch">수정</button>
                <button type="submit" class="no-btn-error" data-method="delete">삭제</button>
            </menu>
        </div>
    </div>
</form>

<?php endSection(); ?>

<?php section('script') ?>
<script>
const form = document.getElementById('frm');

form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const fd = new FormData(form);
    const method = e.submitter.dataset.method;
    if (!method) return;
    fd.set('_method', method);

    const res = await fetch(`/admin/banners/${fd.get('id')}`, {
        method: 'POST',
        body: fd
    });

    const result = await res.json();
    alert(result.message);

    if (result.success) {
        if (method === 'delete') {
            location.href = '/admin/banners';
        } else {
            location.reload();
        }
    }
});
</script>
<?php endSection(); ?>
