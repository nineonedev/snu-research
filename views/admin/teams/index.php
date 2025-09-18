<?php

use app\core\Config;
use app\facades\DB;
use app\models\TeamLang;

    extend('admin'); 
    section('content');
?>

<section class="no-section-sm">
    <div class="no-container-xl">
        <div class="no-admin-content-top">
            <h1 class="no-heading-xs">연구팀 관리</h1>
            <div>
                <a href="/admin/teams/create" class="no-btn-primary">
                    <span>연구팀 생성하기</span>
                </a>
            </div>
        </div>
    </div>
</section>
<section>
    <div class="no-container-xl">
        <div class="no-admin-box">
            <h2 class="no-heading-xxs">연구팀 목록</h2>
            <div class="no-mg-16--t">
                <table class="no-table">
                    <thead>
                        <tr>
                            <th scope="col" width="8%">번호</th>
                            <th scope="col" width="20%">이미지</th>
                            <th scope="col" width="*">이름</th>
                            <th scope="col" width="20%">생성일</th>
                            <th scope="col" width="150">관리</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($rows) : ?>
                        <?php foreach ($rows as $row): 
                            $lang = DB::table('no_team_langs')
                                    ->where('team_id', '=', $row['id'])
                                    ->where('locale', '=', Config::get('default_locale'))
                                    ->first();
                            
                        ?>
                        <tr>
                            <td><?=$row['_no']?></td>
                            <td>
                                <?php if($row['image']) :?>
                                <div class="no-img-table-box">
                                    <img src="<?=UPLOAD_URL.'/'.ltrim($row['image'], '/')?>" alt="">
                                </div>
                                <?php else: ?>
                                -
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= $lang['name']?>
                            </td>
                            <td>
                                <?=formatDate($row['created_at'])?>
                            </td>
                            <td>
                                <menu class="no-admin-action">
                                    <a href="/admin/teams/edit/<?=$row['id']?>" class="no-btn-white">
                                        <span>수정</span>
                                    </a>
                                    <button type="button" class="no-btn-error" data-id="<?=$row['id']?>">
                                        <span>삭제</span>
                                    </button>
                                </menu>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif;?>
                    </tbody>
                </table>
            </div>
            <div class="no-mg-16--t">
                <?= $pagination ?>
            </div>
        </div>
    </div>
</section>


<?php endSection() ?>

<?php section('script') ?>

<script>
    const deleteIds= document.querySelectorAll('[data-id]');

    const handleSubmit = async (e) => {
        e.preventDefault(); 

        if(!confirm('정말로 삭제하시겠습니까?')) return;

        const id = e.currentTarget.dataset.id;

        const fd = new FormData(); 
        fd.set('_method', 'delete'); 

        const response = await fetch(`/admin/teams/${id}`, {
            method: 'post', 
            body: fd
        }); 

        const resData = await response.json();
        const {message, success, data} = resData;

        
        alert(message); 
        

        if(success) {
            location.reload();  
        }
    }


    deleteIds.forEach((item) => {
        item.addEventListener('click', handleSubmit);
    })

</script>

<?php endSection() ?>