<?php


    extend('auth');
    section('content');

?>

<div class="no-container-sm">
    <div class="no-admin-auth-inner">
        <h1 class="no-heading-md no-clr-primary-main --tac">관리자 로그인</h1>
    
        <form method="post" class="no-mg-16--t no-admin-auth-form" id="frm">

            <div class="no-form-control">
                <label for="username" class="no-label">아이디</label>
                <input type="text" name="username" id="username" autofocus>
            </div>
            <div class="no-form-control">
                <label for="password" class="no-label">비밀번호</label>
                <input type="password" name="password" id="password">
            </div>
            
            <div class="no-mg-sm--t">
                <button type="submit" class="no-btn-primary no-btn-full no-btn-md">
                    <span>로그인</span>
                </button>
            </div>
        </form>
    </div>
</div>

<?php endSection(); ?>

<?php section('script') ?>

<script>
    const form = document.getElementById('frm');
    
    const handleSubmit = async (e) => {
        e.preventDefault(); 
        const fd = new FormData(form); 

        const response = await fetch('/auth/signin', {
            method: 'post', 
            body: fd
        }); 

        const resData = await response.json(); 
        const {message, success} = resData; 
        
        alert(message); 

        if(success){
            location.href = "/admin/";
        }
    }
    
    form.addEventListener('submit', handleSubmit);
</script>

<?php endSection() ?>