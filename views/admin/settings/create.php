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
            <h1 class="no-heading-xs">사이트 정보 등록</h1>
        </div>
    </div>
</section>

<form method="post" enctype="multipart/form-data" id="frm">
    <section class="no-section-sm--b">
        <div class="no-container-md">
            <div class="no-admin-box">
                <?= csrf_token() ?>

                <!-- 탭 메뉴 -->
                <div class="no-tab-menu no-mg-12--b" data-tab-menu="locale-tabs">
                    <?php foreach ($locales as $locale => $label): ?>
                        <button type="button" class="no-chip <?= $locale === $defaultLocale ? 'active' : '' ?>">
                            <?= strtoupper($label) ?>
                        </button>
                    <?php endforeach; ?>
                </div>

                <!-- 탭 컨텐츠 -->
                <div id="locale-tabs">
                    <?php foreach ($locales as $locale => $label): ?>
                    <div class="no-tab-section <?= $i === 0 ? 'active' : '' ?>">
                        <h3 class="no-heading-xxs no-mg-16--b"><?= strtoupper($label) ?> 정보</h3>

                        <div class="no-form-control">
                            <label for="site_name_<?= $locale ?>">사이트명</label>
                            <input type="text" name="langs[<?= $locale ?>][site_name]" id="site_name_<?= $locale ?>">
                        </div>

                        <div class="no-form-control">
                            <label for="tel_<?= $locale ?>">전화번호</label>
                            <input type="text" name="langs[<?= $locale ?>][tel]" id="tel_<?= $locale ?>">
                        </div>

                        <div class="no-form-control">
                            <label for="fax_<?= $locale ?>">팩스</label>
                            <input type="text" name="langs[<?= $locale ?>][fax]" id="fax_<?= $locale ?>">
                        </div>

                        <div class="no-form-control">
                            <label for="address_<?= $locale ?>">주소</label>
                            <input type="text" name="langs[<?= $locale ?>][address]" id="address_<?= $locale ?>">
                        </div>

                        <div class="no-form-control">
                            <label for="youtube_link_<?= $locale ?>">유튜브 링크</label>
                            <input type="text" name="langs[<?= $locale ?>][youtube_link]" id="youtube_link_<?= $locale ?>">
                        </div>

                        <div class="no-form-control">
                            <label for="meta_title_<?= $locale ?>">메타 제목</label>
                            <input type="text" name="langs[<?= $locale ?>][meta_title]" id="meta_title_<?= $locale ?>">
                        </div>

                        <div class="no-form-control">
                            <label for="meta_keywords_<?= $locale ?>">메타 키워드</label>
                            <input type="text" name="langs[<?= $locale ?>][meta_keywords]" id="meta_keywords_<?= $locale ?>">
                        </div>

                        <div class="no-form-control">
                            <label for="meta_description_<?= $locale ?>">메타 설명</label>
                            <textarea name="langs[<?= $locale ?>][meta_description]" id="meta_description_<?= $locale ?>"></textarea>
                        </div>

                        <div class="no-form-control">
                            <label for="meta_image_<?= $locale ?>">대표 이미지</label>
                            <input type="file" name="meta_image_<?= $locale ?>" id="meta_image_<?= $locale ?>">
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

            </div>
        </div>
    </section>

    <div class="no-section-lg--b">
        <div class="no-container-md">
            <menu class="no-admin-action">
                <button type="submit" class="no-btn-primary"><span>등록</span></button>
            </menu>
        </div>
    </div>
</form>

<?php endSection() ?>

<?php section('script') ?>
<script>
    const form = document.getElementById('frm');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const fd = new FormData(form);
        const response = await fetch('/admin/settings', {
            method: 'POST',
            body: fd
        });

        const res = await response.json();
        alert(res.message);

        if (res.success) {
            location.reload(); // 또는 /admin/settings 로 리디렉션
        }
    });
</script>
<?php endSection() ?>
