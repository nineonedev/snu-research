<?php

use app\core\Config;

$locales = Config::get('locales');
$defaultLocale = Config::get('default_locale');

extend('admin');
section('content');

?>

<section class="no-section-sm">
    <div class="no-container-md">
        <div class="no-admin-content-top">
            <h1 class="no-heading-xs">사이트 정보 수정</h1>
        </div>
    </div>
</section>

<form method="post" enctype="multipart/form-data" id="frm">
    <input type="hidden" name="id" value="<?= $data['id'] ?>">
    <input type="hidden" name="_method" value="patch">
    <section class="no-section-sm--b">
        <div class="no-container-md">
            <div class="no-admin-box">
                <?= csrf_token() ?>

                <div class="no-tab-menu no-mg-12--b" data-tab-menu="locale-tabs">
                    <?php foreach ($locales as $locale => $label): ?>
                        <button type="button" class="no-chip <?= $locale === $defaultLocale ? 'active' : '' ?>">
                            <?= strtoupper($label) ?>
                        </button>
                    <?php endforeach; ?>
                </div>

                <div id="locale-tabs">
                    <?php foreach ($locales as $locale => $label): 
                        $lang = $langs[array_search($locale, array_column($langs, 'locale'))] ?? [];
                    ?>
                    <div class="no-tab-section <?= $locale === $defaultLocale ? 'active' : '' ?>">
                        <h3 class="no-heading-xxs no-mg-16--b"><?= strtoupper($label) ?> 정보</h3>

                        <div class="no-form-control">
                            <label>사이트명</label>
                            <input type="text" name="langs[<?= $locale ?>][site_name]" value="<?= $lang['site_name'] ?? '' ?>">
                        </div>

                        <div class="no-form-control">
                            <label>전화번호</label>
                            <input type="text" name="langs[<?= $locale ?>][tel]" value="<?= $lang['tel'] ?? '' ?>">
                        </div>

                        <div class="no-form-control">
                            <label>팩스</label>
                            <input type="text" name="langs[<?= $locale ?>][fax]" value="<?= $lang['fax'] ?? '' ?>">
                        </div>

                        <div class="no-form-control">
                            <label>주소</label>
                            <input type="text" name="langs[<?= $locale ?>][address]" value="<?= $lang['address'] ?? '' ?>">
                        </div>

                        <div class="no-form-control">
                            <label>유튜브 링크</label>
                            <input type="text" name="langs[<?= $locale ?>][youtube_link]" value="<?= $lang['youtube_link'] ?? '' ?>">
                        </div>

                        <div class="no-form-control">
                            <label>메타 제목</label>
                            <input type="text" name="langs[<?= $locale ?>][meta_title]" value="<?= $lang['meta_title'] ?? '' ?>">
                        </div>

                        <div class="no-form-control">
                            <label for="meta_keywords_<?= $locale ?>">메타 키워드</label>
                            <input type="text" name="langs[<?= $locale ?>][meta_keywords]" id="meta_keywords_<?= $locale ?>" value="<?= $lang['meta_keywords'] ?? '' ?>">
                        </div>


                        <div class="no-form-control">
                            <label>메타 설명</label>
                            <textarea name="langs[<?= $locale ?>][meta_description]"><?= $lang['meta_description'] ?? '' ?></textarea>
                        </div>

                        <div class="no-form-control">
                            <label>대표 이미지</label>
                            <input type="file" name="meta_image_<?= $locale ?>">

                            <?php if (!empty($lang['meta_image'])) : ?>
                            <div class="no-form-image no-mg-8--t">
                                <div class="no-form-image-box">
                                    <img src="<?= UPLOAD_URL . '/' . $lang['meta_image'] ?>" alt="">
                                </div>
                                <div class="no-mg-4--t">
                                    <div class="no-form-check">
                                        <label>
                                            <input type="checkbox" name="delete_meta_image[<?= $locale ?>]" value="1">
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
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <div class="no-section-lg--b">
        <div class="no-container-md">
            <menu class="no-admin-action">
                <button type="submit" class="no-btn-primary"><span>저장</span></button>
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
        const id = fd.get('id');

        const response = await fetch(`/admin/settings/${id}`, {
            method: 'POST',
            body: fd
        });

        const res = await response.json();
        alert(res.message);

        if (res.success) {
            location.reload();
        }
    });
</script>
<?php endSection(); ?>
