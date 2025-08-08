<?php

use app\core\Config;

    extend('admin'); 
    section('content');

?>

<section class="no-section-sm">
    <div class="no-container-md">
        <div class="no-admin-content-top">
            <h1 class="no-heading-xs">연구팀 생성</h1>
            
        </div>
    </div>
</section>

<form method="post" enctype="multipart/form-data" id="frm">
    <section class="no-section-sm--b">
        <div class="no-container-md">
            <div class="no-admin-box">
                <div>
                    <?= csrf_token() ?>
                    
                    <?php foreach (Config::get('locales') as $locale => $label): ?>
                        <div class="no-form-control">
                            <label for="name_<?= $locale ?>">이름 (<?= strtoupper($label) ?>)</label>
                            <input type="text" name="langs[<?= $locale ?>][name]" id="name_<?= $locale ?>">
                        </div>
                    <?php endforeach; ?>


                    <div class="no-form-control">
                        <label for="image">파일</label>
                        <input type="file" name="image" id="image">
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
                    <!-- group -->

                </div>

            </div>
        </div>
    </section>

    <div class="no-section-lg--b">
        <div class="no-container-md">
            <menu class="no-admin-action">
                <a href="./" class="no-btn-white">
                    <span>취소</span>
                </a>
                <button type="submit" class="no-btn-primary">
                    <span>생성</span>
                </button>
            </menu>
        </div>
    </div>
</form>


<?php endSection() ?>

<?php section('script') ?>
<script>
    const form = document.getElementById('frm'); 

    const handleSubmit = async (e) => {
        e.preventDefault(); 
        const fd = new FormData(form); 


        const response = await fetch('/admin/teams/', {
            method: 'post', 
            body: fd
        }); 

        const resData = await response.json();
        const {message, success, data} = resData;
    
        
        alert(message); 

        if(success) {
            location.href = `/admin/teams/edit/${data.id}`; 
        }
    }

    

    form.addEventListener('submit', handleSubmit);

</script>
<?php endSection() ?>