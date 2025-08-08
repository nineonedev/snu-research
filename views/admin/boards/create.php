<?php

use app\core\Config;
use app\facades\DB;
use app\models\Team;

    extend('admin'); section('content');

?>

<section class="no-section-sm">
    <div class="no-container-md">
        <div class="no-admin-content-top">
            <h1 class="no-heading-xs">게시판 생성</h1>
        </div>
    </div>
</section>

<form action="/admin/boards" method="post" enctype="multipart/form-data" id="frm">
    <section class="no-section-xs--b">
        <div class="no-container-md">
            <div class="no-admin-box">
                <?= csrf_token() ?>

                <?php foreach (Config::get('locales') as $locale => $label): ?>
                    <div class="no-form-control">
                        <label>이름 (<?= strtoupper($label) ?>) </label>
                        <input type="text" name="langs[<?= $locale ?>][name]">
                    </div>
                <?php endforeach; ?>

                <div class="no-form-control">
                    <label for="team_id">소속 팀 ID</label>
                    <select name="team_id" id="team_id">
                        <option value="">선택</option>
                        <?php foreach (Team::all() as $team) :
                            $lang = DB::table('no_team_langs')
                                ->where('team_id', '=', $team->id)
                                ->where('locale', '=', Config::get('default_locale'))
                                ->first();
                        ?>
                        <option value="<?=$team->id?>"><?=$lang['name']?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="no-form-control">
                    <label for="skin">스킨</label>
                    <select type="text" name="skin" id="skin" required>
                        <?php foreach (Config::get('skins') as $skin => $label) :?>
                        <option value="<?=$skin?>"><?=$label?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="no-form-control">
                    <label for="image">썸네일 이미지</label>
                    <input type="file" name="image" id="image">
                </div>

                <div class="no-form-control">
                    <label for="search_key">고유 키</label>
                    <input type="text" name="search_key" id="search_key">
                </div>

                <div class="no-col-6 no-col-md-12">
                    <div class="no-form-label">공용으로사용 여부</div>
                    <div class="no-form-list">
                        <div class="no-form-switch">
                            <label for="is_public">
                                <input type="checkbox" name="is_public" id="is_public" value="1">
                                <div class="no-form-switch-box">
                                    <span class="no-form-switch-knob"></span>
                                </div>
                                <span class="no-form-switch-text">연구팀이 아닌 공용게시판으로 사용합니다.</span>
                            </label>
                        </div>
                    </div>
                </div>
                

                <div class="no-row">
                    <?php for ($i = 1; $i <= 10; $i++): ?>
                        <div class="no-form-control no-col-6 no-col-md-12">
                            <label>추가필드 <?=$i?> </label>
                            <input type="text" name="extra_<?=$i?>">
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </section>

    <div class="no-section-lg--b">
        <div class="no-container-md">
            <menu class="no-admin-action">
                <a href="/admin/boards" class="no-btn-white">
                    <span>취소</span>
                </a>
                <button type="submit" class="no-btn-primary">
                    <span>생성</span>
                </button>
            </menu>
        </div>
    </div>
</form>

<?php endSection(); ?>


<?php section('script') ?>
<script>
    const form = document.querySelector('#frm');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(form);

        const response = await fetch(form.action, {
            method: form.method || 'POST',
            body: formData,
        });

        const result = await response.json();

        alert(result.message);

        if (result.success) {
            location.href = '/admin/boards';
        }
    });
</script>
<?php endSection(); ?>
