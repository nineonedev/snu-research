<?php

use app\core\Session;

extend('admin');
section('content');

?>

<section class="no-section-sm">
    <div class="no-container-md">
        <div class="no-admin-content-top">
            <h1 class="no-heading-xs">관리자 수정</h1>
        </div>
    </div>
</section>

<form method="post" id="frm">
    <?= csrf_token() ?>
    <input type="hidden" name="id" value="<?= $data->id ?>">

    <section class="no-section-sm--b">
        <div class="no-container-md">
            <div class="no-admin-box">
                <div class="no-form-control">
                    <label for="name">이름</label>
                    <input type="text" name="name" id="name" value="<?= htmlspecialchars($data->name) ?>">
                </div>

                <div class="no-form-control">
                    <label for="username">아이디</label>
                    <input type="text" name="username" id="username" value="<?= htmlspecialchars($data->username) ?>" readonly>
                </div>

                <div class="no-form-control">
                    <label for="password">비밀번호 변경 (선택)</label>
                    <input type="password" name="password" id="password" placeholder="변경 시 입력하세요">
                </div>

                <?php if(Session::get('role_id') == ROLE_OWNER) :?>
                <div class="no-form-control">
                    <label for="team_id">소속 팀</label>
                    <select name="team_id" id="team_id">
                        <option value="">선택</option>
                        <?php foreach ($teams as $team): ?>
                            <option value="<?= $team->id ?>" <?= $team->id == $data->team_id ? 'selected' : '' ?>>
                                <?= htmlspecialchars($team->lang['name'] ?? '이름 없음') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php else: ?>
                <input type="hidden" name="team_id" value="<?=$data->team_id?>">
                <?php endif; ?>

            </div>
        </div>
    </section>

    <div class="no-section-lg--b">
        <div class="no-container-md">
            <menu class="no-admin-action">
                <?php if(Session::get('role_id') == ROLE_OWNER):?>
                <a href="/admin/admins" class="no-btn-white">취소</a>
                <?php endif; ?>
                
                <button type="submit" class="no-btn-primary" data-method="patch">수정</button>
                <?php if(Session::get('role_id') == ROLE_OWNER):?>
                <button type="submit" class="no-btn-error" data-method="delete">삭제</button>
                <?php endif; ?>
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

    if (method === 'delete') {
        const confirmed = confirm('정말 삭제하시겠습니까? 이 작업은 되돌릴 수 없습니다.');
        if (!confirmed) return;
    }

    fd.set('_method', method);

    const res = await fetch(`/admin/admins/${fd.get('id')}`, {
        method: 'POST',
        body: fd
    });

    const result = await res.json();
    alert(result.message);

    if (result.success) {
        if (method === 'delete') {
            location.href = '/admin/admins';
        } else {
            location.reload();
        }
    }
});
</script>
<?php endSection(); ?>
