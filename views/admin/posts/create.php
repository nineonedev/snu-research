<?php

use app\core\Config;
use app\facades\DB;
use app\models\Board;

extend('admin');
section('content');

$locales = Config::get('locales');
$defaultLocale = Config::get('default_locale');

$boardId = $_GET['board_id'] ?? null;
$isPublic = $_GET['is_public'] ?? 0;
$teamId = $_GET['team_id'] ?? null;

// 게시판 목록 가져오기
$publicBoards = DB::table('no_boards')
    ->whereNull('team_id')
    ->where('is_public', '=', 1)
    ->get();
$teamBoards = [];


if ($teamId) {
    $teamBoards = DB::table('no_boards')->where('team_id', '=', $teamId)->get();
}

?>

<section class="no-section-sm">
    <div class="no-container-md">
        <div class="no-admin-content-top">
            <h1 class="no-heading-xs">
                <?=$pageTitle?>
            </h1>
        </div>
    </div>
</section>

<form method="post" enctype="multipart/form-data" id="frm">
    <?= csrf_token() ?>

    <section class="no-section-sm--b">
        <div class="no-container-md">
            <div class="no-admin-box no-mg-16--b">

                <!-- 팀 및 게시판 선택 -->
                <div class="no-form-control">
                    <?php if ($teamId): ?>
                        <input type="hidden" name="team_id" value="<?= $teamId ?>">

                        <label for="board_id">게시판 선택</label>
                        <select name="board_id" id="board_id" required>
                            <option value="">선택</option>
                            <?php foreach ($teamBoards as $board): 
                                $lang = DB::table('no_board_langs')
                                    ->where('board_id', '=', $board['id'])
                                    ->where('locale', '=', $defaultLocale)
                                    ->first();
                            ?>
                                <option value="<?= $board['id'] ?>" >
                                    <?= $lang['name'] ?? '제목 없음' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                    <?php elseif ($isPublic): ?>
                        <!-- 공용 게시판 선택 -->
                        <label for="board_id">공용 게시판 선택</label>
                        <select name="board_id" id="board_id" required>
                            <option value="">선택</option>
                            <?php foreach ($publicBoards as $board): 
                                $lang = DB::table('no_board_langs')
                                    ->where('board_id', '=', $board['id'])
                                    ->where('locale', '=', $defaultLocale)
                                    ->first();
                                $selected = $board['id'] === $boardId ? 'selected' : '';
                            ?>
                                <option value="<?= $board['id'] ?>" <?=$selected?>>
                                    <?= $lang['name'] ?? '제목 없음' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                    <?php else: ?>
                        <!-- 게시판 직접 설정 (숨겨진 값) -->
                        <input type="hidden" name="board_id" value="<?= $boardId ?>">
                        <?php 
                            $lang = DB::table('no_board_langs')
                                ->where('board_id', '=', $boardId)
                                ->where('locale', '=', $defaultLocale)
                                ->first();
                        ?>
                        <p><strong>게시판:</strong> <?= $lang['name'] ?? '제목 없음' ?></p>
                    <?php endif; ?>
                </div>

                <div class="no-form-control">
                    <label for="link_url">링크 URL</label>
                    <input type="text" name="link_url" id="link_url" placeholder="링크 URL">
                </div>

                <div>
                    <div class="no-form-label">공지여부</div>
                    <div class="no-form-list">
                        <div class="no-form-switch">
                            <label for="is_notice">
                                <input type="checkbox" name="is_notice" id="is_notice" value="1">
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
                                <input type="checkbox" name="is_hidden" id="is_hidden" value="1">
                                <div class="no-form-switch-box">
                                    <span class="no-form-switch-knob"></span>
                                </div>
                                <span class="no-form-switch-text">화면에서 일시적으로 숨깁니다.</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="no-form-control">
                    <label for="image">대표 이미지</label>
                    <input type="file" name="image" id="image">
                </div>
            </div>

            <div class="no-admin-box no-mg-16--b">

                <!-- 언어 탭 메뉴 -->
                <div class="no-tab-menu no-mg-20--b" data-tab-menu="locale-tabs">
                    <?php foreach ($locales as $locale => $label): ?>
                        <button type="button" class="no-chip <?= $locale === $defaultLocale ? 'active' : '' ?>">
                            <?= strtoupper($label) ?>
                        </button>
                    <?php endforeach; ?>
                </div>

                <!-- 언어별 입력 폼 -->
                <div id="locale-tabs">
                    <?php foreach ($locales as $locale => $label): ?>
                    <div class="no-tab-section">
                        <div class="no-form-control">
                            <label for="title_<?= $locale ?>">제목 (<?= strtoupper($label) ?>)</label>
                            <input type="text" name="langs[<?= $locale ?>][title]" id="title_<?= $locale ?>" placeholder="제목">
                        </div>
                        <div class="no-form-control">
                            <label for="content_<?= $locale ?>">내용 (<?= strtoupper($label) ?>)</label>
                            <textarea name="langs[<?= $locale ?>][content]" id="content_<?= $locale ?>" placeholder="내용" class="summernote"></textarea>
                        </div>

                        <div class="no-row" data-extra-hook="<?=$locale?>"></div>

                        <hr>

                        <!-- 이미지 업로드 -->
                        <div class="no-row">
                            <?php for ($i = 1; $i <= 10; $i++): ?>
                                <div class="no-form-control no-col-6 no-col-md-12">
                                    <label for="image_<?= $locale ?>_<?= $i ?>">이미지 <?= $i ?> (<?= strtoupper($label) ?>)</label>
                                    <input type="file" name="image_<?= $locale ?>_<?= $i ?>" id="image_<?= $locale ?>_<?= $i ?>">
                                </div>
                            <?php endfor; ?>
                        </div>

                                
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    </section>

    <!-- 액션 버튼 -->
    <div class="no-section-lg--b">
        <div class="no-container-md">
            <menu class="no-admin-action">
                <a href="/admin/posts/?<?=http_build_query($_GET)?>" class="no-btn-white">취소</a>
                <button type="submit" class="no-btn-primary">생성</button>
            </menu>
        </div>
    </div>
</form>

<?php endSection(); ?>

<?php section('script') ?>
<script>

const locales = <?= json_encode(array_keys($locales)) ?>;
const localeLabels = <?= json_encode(array_values($locales)) ?>;
const defaultLocale = '<?= $defaultLocale ?>';

const boardIdEl = document.getElementById('board_id');

boardIdEl.addEventListener('change', async function () {
    const boardId = this.value;
    if (!boardId) return;

    const res = await fetch(`/admin/boards/${boardId}/extras`);
    const result = await res.json();

    if (!result.success) {
        alert('추가 필드를 불러오는 데 실패했습니다.');
        return;
    }

    const extras = result.extras; // { extra1: '라벨명', ... }

    locales.forEach((locale, index) => {
        const labelText = localeLabels[index]; // ex: "한국어"
        const wrapper = document.querySelector(`[data-extra-hook="${locale}"]`);
        if (!wrapper) return;

        wrapper.innerHTML = ''; // 기존 필드 초기화

        for (let i = 1; i <= 10; i++) {
            const fieldKey = `extra${i}`;
            const fieldLabel = extras[fieldKey];
            if (!fieldLabel) continue; // 값이 없으면 렌더링하지 않음

            const html = `
                <div class="no-form-control no-col-6 no-col-md-12">
                    <label for="${fieldKey}_${locale}">${fieldLabel} (${labelText})</label>
                    <input type="text" name="langs[${locale}][${fieldKey}]" id="${fieldKey}_${locale}" placeholder="${fieldLabel}">
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

    const res = await fetch('/admin/posts', {
        method: 'post',
        body: fd
    });

    const result = await res.json();
    alert(result.message);

    if (result.success) {
        const params = new URLSearchParams(location.search); 
        params.set('board_id', fd.get('board_id')); 
        location.href = `/admin/posts/edit/${result.data.id}?${params.toString()}`;
    }
});
</script>
<?php endSection(); ?>
