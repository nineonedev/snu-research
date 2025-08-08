<?php 

use app\core\Config;

?>

<footer class="no-footer">
    <div class="no-container-xl">
        <div class="no-footer__inner">
            <?php if (count(MENU_ITEMS) > 0) : ?>
                <ul class="f-menu no-mg-24--b">
                    <?php foreach (MENU_ITEMS as $di => $depth) :
                        $depth_active = $depth['isActive'] ? 'active' : '';
                    ?>
                        <li>
                            <a href="<?= $depth['path'] ?>" class="no-body-sm"><?= $depth['title'] ?></a>
                        </li>
                    <?php endforeach; ?>

                    <!-- <li class="check-wrap">
                        <a href="#" class="no-body-sm" onclick="return false">개인정보처리방침</a>
                    </li> -->
                </ul>
            <?php endif; ?>

            <div class="f-wrap no-pd-24--t">
                <div class="f-info">
                    <h2 class="no-mg-8--b no-base-snum"><?=Config::get('setting')['site_name']?></h2>
                    <ul class="info-list">
                        <li class="no-base-finfo">
                        <?=Config::get('setting')['address']?>
                        </li>

                        <li class="no-base-finfo">
                            TEL. <?=Config::get('setting')['tel']?>
                        </li>

                        <li class="no-base-finfo">
                            FAX. <?=Config::get('setting')['fax']?>
                        </li>
                    </ul>
                </div>

                <ul class="sns-list">
                    <li>
                        <a href="<?=Config::get('setting')['youtube_link']?>" target="_blank">
                            <img src="<?=img('icon/b-youtube.svg')?>">
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="no-footer__bottom no-pd-18--y">
        <div class="no-container-xl">
            <copy class="no-body-xs">Copyright (C) 2025 SNU CONTEMPORARY KOREAN STUDIES. All Rights Reserved.</copy>

            <div class="lang-wrap">
                <?php 
                    $i = 0; 
                    foreach (Config::get('locales') as $key => $label):  ?>
                <a href="/<?=$key === Config::get('default_locale') ? '' : $key?>">
                    <?= strtoupper($key) ?>
                </a>
                <?php if($i < 1): ?>
                    <span></span>
                <?php endif; ?>
                <?php $i++; endforeach;  ?>
            </div>
        </div>
    </div>
</footer>

<!-- <div class="form-popup">
    <i class="fa-sharp fa-light fa-xmark-large p-close" style="color: #000000;"></i>
    <h2 class="title">
        <p>개인정보처리방침</p>
    </h2>
    <div class="content" data-lenis-prevent-wheel>
        <div class="scroll-box" data-lenis-prevent-wheel>
            <ul>
                <li>
                    <h3>제1조 (개인정보 수집에 대한 동의)</h3>
                    <p>하이퍼웨이브는 이용자들이 회사의 개인정보취급방침 또는 이용약관의 내용에 대하여 “동의”버튼을 클릭하면 개인정보 수집에 대해 동의한 것으로 봅니다.</p>
                </li>

                <li>
                    <h3>
                        제2조 (개인정보 수집항목)</h3>
                    <p>온라인 문의를 통한 상담을 위해 처리하는 개인정보 항목은 아래와 같습니다.<br>
                        수집항목: 이름, 전화번호, 이메일</p>
                </li>

                <li>
                    <h3>제3조 (개인정보의 이용목적)</h3>
                    <p> 회사는 이용자의 사전 동의 없이는 이용자의 개인 정보를 공개하지 않으며, 원활한 고객상담, 각종 서비스의 제공을 위해 아래와 같이 개인정보를 수집하고 있습니다.
                        모든 정보는 상기 목적에 필요한 용도 이외로는 사용되지 않으며 수집 정보의 범위나 사용 목적, 용도가 변경될 시에는 반드시 사전 동의를 구할 것입니다.<br><br>


                        성명: 상담에 따른 본인 확인<br>
                        전화번호: 상담 및 이벤트 관련 고지사항 전달, 새로운 서비스 및 정보 제공(DM, SMS, 이메일 등 이용)
                        이용자는 개인정보의 수집/이용에 대한 동의를 거부할 수 있습니다.<br>
                        다만, 동의를 거부하는 경우 온라인 문의를 통한 상담은 불가하며 서비스 이용 및 혜택 제공에 제한을 받을 수 있습니다.</p>
                </li>

                <li>
                    <h3>
                        제4조 (개인정보의 보유 및 이용기간)</h3>
                    <p>
                        원칙적으로 개인정보 수집 및 이용목적이 달성된 후에는 해당 정보를 지체 없이 파기합니다.
                        그리고 상법, 전자상거래 등에서의 소비자보호에 관한 법률 등 관계법령의 규정에 의하여 보존할 필요가 있는 경우 회사는 관계법령에서 정한 일정한 기간 동안 정보를 보관합니다.
                        이 경우 회사는 보관하는 정보를 그 보관의 목적으로만 이용하며 보존기간은 아래와 같습니다.<br><br>

                        계약 또는 청약철회 등에 관한 기록: 5년(전자상거래등에서의 소비자보호에 관한 법률)
                        소비자의 불만 또는 분쟁처리에 관한 기록: 3년(전자상거래등에서의 소비자 보호에 관한 법률)
                        시용정보의 수집/처리 및 이용 등에 관한 기록: 3년(신용정보의 이용 및 보호에 관한 법률)
                        회사는 귀중한 이용자의 개인정보를 안전하게 처리하며, 유출의 방지를 위하여 다음과 같은 방법을 통하여 개인정보를 파기합니다.
                        종이에 출력된 개인정보는 분쇄기로 분쇄하거나 소각을 통하여 파기합니다.
                        전자적 파일 형태로 저장된 개인정보는 기록을 재생할 수 없는 기술적 방법을 사용하여 삭제합니다.</p>
            </ul>
        </div>
    </div>
</div> -->

<div class="popup-bg"></div>

</body>

</html>