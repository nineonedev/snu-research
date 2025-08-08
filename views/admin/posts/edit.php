<?php

use app\core\Config;
use app\facades\DB;
use app\models\Board;
use app\models\BoardLang;
use app\models\Team;


extend('admin');
section('content');

$locales = Config::get('locales');
$defaultLocale = Config::get('default_locale');

$boardId = $_GET['board_id'] ?? null;
$board = DB::table('no_boards')->where('id', '=', $boardId)->first();
$isPublic = $_GET['is_public'] ?? 0;
$teamId = $_GET['team_id'] ?? null;

if ($teamId) {
    $teamBoards = DB::table('no_boards')->where('team_id', '=', $teamId)->get();
} else {
    $teamBoards = [];
}

$publicBoards = DB::table('no_boards')->where('is_public', '=', 1)->whereNull('team_id')->get();
$boards = DB::table('no_boards')->whereNull('team_id')->get();

?>

<section class="no-section-sm">
    <div class="no-container-md">
        <div class="no-admin-content-top">
            <h1 class="no-heading-xs">
            <?=$pageTitle?>
            </h1>
            <a href="/admin/posts/create?<?=http_build_query($_GET)?>" class="no-btn-black">
                <span>게시글 생성하기</span>
            </a>
        </div>
    </div>
</section>

<form method="post" enctype="multipart/form-data" id="frm">
    <?= csrf_token() ?>
    <input type="hidden" name="id" value="<?= $data['id'] ?>">
    <input type="hidden" name="_method" value="patch">

    <section class="no-section-sm--b">
        <div class="no-container-md">
            <div class="no-admin-box no-mg-16--b">
                <!-- 팀 이름 및 게시판 선택 영역 -->
                <div class="no-form-control">
                    <?php if ($isPublic): ?>
                        <!-- 공용 게시판인 경우 select -->
                        <label for="board_id">공용 게시판 선택</label>
                        <select name="board_id" id="board_id" required>
                            <option value="">선택</option>
                            <?php foreach ($publicBoards as $b):
                                $lang = DB::table('no_board_langs')->where('board_id', '=', $b['id'])->where('locale', '=', $defaultLocale)->first();
                            ?>
                                <option value="<?= $b['id'] ?>" <?= $boardId == $b['id'] ? 'selected' : '' ?>>
                                    <?= $lang['name'] ?? '제목 없음' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php elseif ($teamId): ?>
                        <label for="board_id">팀 게시판 선택</label>
                        <select name="board_id" id="board_id" required>
                            <option value="">선택</option>
                            <?php foreach ($teamBoards as $b):
                                $lang = DB::table('no_board_langs')->where('board_id', '=', $b['id'])->where('locale', '=', $defaultLocale)->first();
                            ?>
                                <option value="<?= $b['id'] ?>" <?= $boardId == $b['id'] ? 'selected' : '' ?>>
                                    <?= $lang['name'] ?? '제목 없음' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php else: ?>
                        <p class="no-color-danger">게시판 정보를 불러올 수 없습니다.</p>
                    <?php endif; ?>
                </div>
                <div class="no-form-control">
                    <label for="image">대표 이미지</label>
                    <input type="file" name="image" id="image">

                    <?php if ($data['image']): ?>
                        <div class="no-form-image no-mg-8--t">
                            <div class="no-form-image-box">
                                <img src="<?= UPLOAD_URL . '/' . $data['image'] ?>" alt="">
                            </div>
                            <div class="no-mg-4--t">
                                <div class="no-form-check">
                                    <label for="delete_image">
                                        <input 
                                            type="checkbox" 
                                            name="delete_image" 
                                            id="delete_image"
                                            value="<?=$data['image']?>"
                                        >
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
                    <label for="link_url">링크 URL</label>
                    <input type="text" name="link_url" id="link_url" value="<?=$data['link_url']?>" placeholder="링크 URL">
                </div>

                <div>
                    <div class="no-form-label">공지여부</div>
                    <div class="no-form-list">
                        <div class="no-form-switch">
                            <label for="is_notice">
                                <input type="checkbox" name="is_notice" id="is_notice" value="1" <?= $data['is_notice'] ? 'checked' : '' ?>>
                                <div class="no-form-switch-box">
                                    <span class="no-form-switch-knob"></span>
                                </div>
                                <span class="no-form-switch-text">게시글의 최상단에 위치됩니다.</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="no-form-label">활성여부</div>
                    <div class="no-form-list">
                        <div class="no-form-switch">
                            <label for="is_hidden">
                                <input type="checkbox" name="is_hidden" id="is_hidden" value="1" <?= $data['is_hidden'] ? 'checked' : '' ?>>
                                <div class="no-form-switch-box">
                                    <span class="no-form-switch-knob"></span>
                                </div>
                                <span class="no-form-switch-text">화면에서 일시적으로 숨깁니다.</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

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
                        $lang = $langs[$locale] ?? [];
                    ?>
                    <div class="no-tab-section">
                        <div class="no-form-control">
                            <label for="title_<?= $locale ?>">제목 (<?= strtoupper($label) ?>)</label>
                            <input type="text" name="langs[<?= $locale ?>][title]" id="title_<?= $locale ?>" value="<?= $lang['title'] ?? '' ?>">
                        </div>
                        <div class="no-form-control">
                            <label for="content_<?= $locale ?>">내용 (<?= strtoupper($label) ?>)</label>
                            <textarea class="summernote" name="langs[<?= $locale ?>][content]" id="content_<?= $locale ?>"><?= $lang['content'] ?? '' ?></textarea>
                        </div>

                        <div class="no-row" data-extra-hook="<?= $locale ?>">
                            <?php for ($i = 1; $i <= 10; $i++): 
                                $fieldKey = "extra{$i}";
                                $fieldLabel = $board[$fieldKey] ?? null;
                                $fieldValue = $lang[$fieldKey] ?? '';
                                if (!$fieldLabel) continue;
                            ?>
                                <div class="no-form-control no-col-6 no-col-md-12">
                                    <label for="<?= $fieldKey ?>_<?= $locale ?>"><?= $fieldLabel ?> (<?= $label ?>)</label>
                                    <input type="text" name="langs[<?= $locale ?>][<?= $fieldKey ?>]" id="<?= $fieldKey ?>_<?= $locale ?>" value="<?= htmlspecialchars($fieldValue) ?>" placeholder="<?= $fieldLabel ?>">
                                </div>
                            <?php endfor; ?>
                        </div>


                        <hr>

                        <div class="no-row">
                            <?php for ($i = 1; $i <= 10; $i++): 
                                $imgKey = "image{$i}";
                            ?>
                                <div class="no-form-control no-col-6 no-col-md-12">
                                    <label for="<?= $imgKey ?>_<?= $locale ?>">이미지 <?= $i ?> (<?= strtoupper($label) ?>)</label>
                                    <input type="file" name="image_<?= $locale ?>_<?= $i ?>" id="<?= $imgKey ?>_<?= $locale ?>">
                                    <?php if (!empty($lang[$imgKey])): ?>
                                        <div class="no-form-image no-mg-8--t">
                                            <div class="no-form-image-box">
                                                <img src="<?= UPLOAD_URL . '/' . $lang[$imgKey] ?>" alt="">
                                            </div>
                                            <div class="no-mg-4--t">
                                                <div class="no-form-check">
                                                    <label>
                                                        <input type="checkbox" name="delete_image_<?= $locale ?>_<?= $i ?>" value="<?=$lang[$imgKey]?>">
                                                        <div class="no-form-check-box"><i class="fa-regular fa-check"></i></div>
                                                        <span class="no-form-check-text">이미지 삭제</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endfor; ?>
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
                <a href="/admin/posts/?<?=http_build_query($_GET)?>" class="no-btn-white">취소</a>
                <button type="submit" class="no-btn-primary">수정</button>
                <button type="button" class="no-btn-error" id="btn-delete">삭제</button>
            </menu>
        </div>
    </div>
</form>

<?php endSection(); ?>

<?php section('script') ?>
<script>

const parmas = new URLSearchParams(location.search);

document.getElementById('btn-delete').addEventListener('click', async () => {
    if (!confirm('정말 삭제하시겠습니까?')) return;

    const fd = new FormData();
    fd.set('_method', 'delete');
    const res = await fetch('/admin/posts/<?= $data['id'] ?>', {
        method: 'post',
        body: fd
    });

    const result = await res.json();
    alert(result.message);


    if (result.success) {
        location.href = `/admin/posts/?${parmas.toString()}`;
    }
});


const locales = <?= json_encode(array_keys($locales)) ?>;
const localeLabels = <?= json_encode(array_values($locales)) ?>;
const defaultLocale = '<?= $defaultLocale ?>';

const boardIdEl = document.getElementById('board_id');
boardIdEl.addEventListener('change', async function () {
    const currentBoardId = this.value;

    if (!currentBoardId) return;

    const res = await fetch(`/admin/boards/${currentBoardId}/extras`);
    const result = await res.json();

    if (!result.success) {
        alert('추가 필드를 불러오는 데 실패했습니다.');
        return;
    }

    const extras = result.extras;
    const existingLangs = <?= json_encode($langs) ?>;
    const originalBoardId = '<?= $boardId ?>';

    locales.forEach((locale, index) => {
        const labelText = localeLabels[index];
        const wrapper = document.querySelector(`[data-extra-hook="${locale}"]`);
        if (!wrapper) return;

        wrapper.innerHTML = '';

        for (let i = 1; i <= 10; i++) {
            const fieldKey = `extra${i}`;
            const fieldLabel = extras[fieldKey];
            if (!fieldLabel) continue;

            const fieldValue = (currentBoardId === originalBoardId)
                ? (existingLangs[locale]?.[fieldKey] || '')
                : '';

            const html = `
                <div class="no-form-control no-col-6 no-col-md-12">
                    <label for="${fieldKey}_${locale}">${fieldLabel} (${labelText})</label>
                    <input type="text" name="langs[${locale}][${fieldKey}]" id="${fieldKey}_${locale}" value="${fieldValue}" placeholder="${fieldLabel}">
                </div>
            `;
            wrapper.insertAdjacentHTML('beforeend', html);
        }
    });
});




const form = document.getElementById('frm');
form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const fd = new FormData(form);
    const res = await fetch(`/admin/posts/${fd.get('id')}`, {
        method: 'post',
        body: fd
    });

    const result = await res.json();
    alert(result.message);

    if (result.success) {
        const params = new URLSearchParams(location.search);

        // 필요한 값만 갱신
        params.set('board_id', fd.get('board_id'));

        // 중복 방지용으로 pathname 기준으로 새 URL 생성
        location.href = location.pathname + '?' + params.toString();
    }
});
</script>
<?php endSection(); ?>
