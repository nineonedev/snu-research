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
            <h1 class="no-heading-xs">배너 생성</h1>
        </div>
    </div>
</section>

<form method="post" enctype="multipart/form-data" id="frm">
    <?= csrf_token() ?>

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
                    <?php foreach ($locales as $locale => $label): ?>
                    <div class="no-tab-section">
                        <div class="no-form-control">
                            <label for="title_<?= $locale ?>">제목 (<?= strtoupper($label) ?>)</label>
                            <input type="text" name="langs[<?= $locale ?>][title]" id="title_<?= $locale ?>" placeholder="타이틀">
                        </div>
                        <div class="no-form-control">
                            <label for="description_<?= $locale ?>">설명 (<?= strtoupper($label) ?>)</label>
                            <input type="text" name="langs[<?= $locale ?>][description]" id="description_<?= $locale ?>" placeholder="타이틀">
                        </div>
                        <div class="no-form-control">
                            <label for="link_<?= $locale ?>">링크 (<?= strtoupper($label) ?>)</label>
                            <input type="text" name="langs[<?= $locale ?>][link]" id="link_<?= $locale ?>" placeholder="타이틀">
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
                            <option value="<?=$key?>"><?=$label?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="no-form-control">
                    <label for="image">이미지</label>
                    <input type="file" name="image" id="image">
                </div>

                <div class="no-form-control">
                    <label for="display_order">정렬순서</label>
                    <input type="number" name="display_order" id="display_order" value="0">
                </div>

                <div class="no-col-6 no-col-md-12">
                    <div class="no-form-label">노출여부</div>
                    <div class="no-form-list">
                        <div class="no-form-switch">
                            <label for="is_hidden">
                                <input type="checkbox" name="is_hidden" id="is_hidden" value="1">
                                <div class="no-form-switch-box">
                                    <span class="no-form-switch-knob"></span>
                                </div>
                                <span class="no-form-switch-text">배너를 숨깁니다.</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="no-section-lg--b">
        <div class="no-container-md">
            <menu class="no-admin-action">
                <a href="/admin/banners" class="no-btn-white">취소</a>
                <button type="submit" class="no-btn-primary">생성</button>
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

    const res = await fetch('/admin/banners', {
        method: 'post',
        body: fd
    });

    const result = await res.json();
    alert(result.message);

    if (result.success) {
        location.href = `/admin/banners/edit/${result.data.id}`;
    }
});
</script>
<?php endSection(); ?>
