<?php

use app\core\Config;
use app\facades\DB;
use app\models\Team;

extend('admin');
section('content');

?>

<section class="no-section-sm">
    <div class="no-container-md">
        <div class="no-admin-content-top">
            <h1 class="no-heading-xs">게시판 수정</h1>
        </div>
    </div>
</section>

<form method="post" enctype="multipart/form-data" id="frm">
    <section class="no-section-sm--b">
        <div class="no-container-md">
            <div class="no-admin-box">
                <?= csrf_token() ?>
                <input type="hidden" name="id" value="<?= $data['id'] ?>">

                <?php foreach ($data['langs'] as $lang): ?>
                    <div class="no-form-control">
                        <label for="name_<?= $lang['locale'] ?>">이름 (<?= strtoupper(Config::get('locales')[$lang['locale']]) ?>)</label>
                        <input 
                            type="text" 
                            name="langs[<?= $lang['locale'] ?>][name]" 
                            id="name_<?= $lang['locale'] ?>" 
                            value="<?= htmlspecialchars($lang['name']) ?>"
                        >
                    </div>
                <?php endforeach; ?>

                <div class="no-form-control">
                    <label for="team_id">소속 팀</label>
                    <select name="team_id" id="team_id">
                        <option value="">선택</option>
                        <?php foreach (Team::all() as $team): 
                            $lang = DB::table('no_team_langs')
                                ->where('team_id', '=', $team->id)
                                ->where('locale', '=', Config::get('default_locale'))
                                ->first();
                        ?>
                        <option value="<?= $team->id ?>" <?= $data['team_id'] == $team->id ? 'selected' : '' ?>>
                            <?= $lang['name'] ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="no-form-control">
                    <label for="skin">스킨</label>
                    <select name="skin" id="skin" required>
                        <?php foreach (Config::get('skins') as $skin => $label): ?>
                        <option value="<?= $skin ?>" <?= $data['skin'] == $skin ? 'selected' : '' ?>>
                            <?= $label ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="no-form-control">
                    <label for="image">썸네일 이미지</label>
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
                    <label for="search_key">고유 키</label>
                    <input type="text" name="search_key" id="search_key" value="<?=$data['search_key']?>">
                </div>

                <div class="no-col-6 no-col-md-12">
                    <div class="no-form-label">공용으로사용 여부</div>
                    <div class="no-form-list">
                        <div class="no-form-switch">
                            <label for="is_public">
                                <input type="checkbox" name="is_public" id="is_public" 
                                <?= $data['is_public'] ? 'checked' : '' ?> value="1">
                                <div class="no-form-switch-box">
                                    <span class="no-form-switch-knob"></span>
                                </div>
                                <span class="no-form-switch-text">연구팀이 아닌 공용게시판으로 사용합니다.</span>
                            </label>
                        </div>
                    </div>
                </div>


                <div class="no-row">
                    <?php for ($i = 1; $i <= 10; $i++): 
                        $key = 'extra' . $i;
                    ?>
                    <div class="no-form-control no-col-6 no-col-md-12">
                        <label>추가필드 <?= $i ?></label>
                        <input type="text" name="extra_<?= $i ?>" value="<?= htmlspecialchars($data[$key] ?? '') ?>">
                    </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </section>

    <div class="no-section-lg--b">
        <div class="no-container-md">
            <menu class="no-admin-action">
                <a href="/admin/boards/?<?=http_build_query($_GET)?>" class="no-btn-white">
                    <span>취소</span>
                </a>
                <button type="submit" class="no-btn-primary" data-method="patch">
                    <span>수정</span>
                </button>
                <button type="submit" class="no-btn-error" data-method="delete">
                    <span>삭제</span>
                </button>
            </menu>
        </div>
    </div>
</form>

<?php endSection(); ?>

<?php section('script'); ?>
<script>
    const form = document.getElementById('frm');

    const handleSubmit = async (e) => {
        e.preventDefault();

        const fd = new FormData(form);
        const method = e.submitter.dataset.method;

        if (!method) return;
        fd.set('_method', method);

        const response = await fetch(`/admin/boards/${fd.get('id')}`, {
            method: 'post',
            body: fd,
        });

        const res = await response.json();
        alert(res.message);

        if (res.success) {
            if (method === 'delete') {
                window.location.href = '/admin/boards';
            } else {
                location.reload();
            }
        }
    };

    form.addEventListener('submit', handleSubmit);
</script>
<?php endSection(); ?>
