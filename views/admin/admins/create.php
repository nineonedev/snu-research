<?php

use app\core\Session;
use app\models\Admin;

extend('admin');
section('content');


?>

<section class="no-section-sm">
    <div class="no-container-md">
        <div class="no-admin-content-top">
            <h1 class="no-heading-xs">관리자 생성</h1>
        </div>
    </div>
</section>

<form method="post" id="frm">
    <?= csrf_token() ?>
    <input type="hidden" name="role_id" value="<?=$role_id?>">

    <section class="no-section-sm--b">
        <div class="no-container-md">
            <div class="no-admin-box">
                <div class="no-form-control">
                    <label for="name">이름</label>
                    <input type="text" name="name" id="name" placeholder="이름을 입력하세요">
                </div>

                <div class="no-form-control">
                    <label for="username">아이디</label>
                    <input type="text" name="username" id="username" placeholder="아이디를 입력하세요">
                </div>

                <div class="no-form-control">
                    <label for="password">비밀번호</label>
                    <input type="password" name="password" id="password" placeholder="비밀번호를 입력하세요">
                </div>

                <div class="no-form-control">
                    <label for="team_id">소속 팀</label>
                    <select name="team_id" id="team_id">
                        <option value="">선택</option>
                        <?php foreach ($teams as $team): ?>
                            <option value="<?= $team->id ?>">
                                <?= htmlspecialchars($team->lang['name'] ?? '이름 없음') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>


            </div>
        </div>
    </section>

    <div class="no-section-lg--b">
        <div class="no-container-md">
            <menu class="no-admin-action">
                <?php if (Session::get('role_id') == ROLE_OWNER): ?>
                <a href="/admin/admins" class="no-btn-white">취소</a>
                <button type="submit" class="no-btn-primary">생성</button>
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

    if(!fd.get('team_id')) {
        alert('소속 팀을 선택해주세요'); 
        return; 
    }

    const res = await fetch('/admin/admins', {
        method: 'POST',
        body: fd
    });

    const result = await res.json();
    alert(result.message);

    if (result.success) {
        location.href = `/admin/admins/`;
    }
});
</script>
<?php endSection(); ?>
