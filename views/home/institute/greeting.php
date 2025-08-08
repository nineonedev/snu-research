<?php section('content') ?>

<section class="about-intro no-pd-120--t">
    <div class="no-container-xl">
        <figure class="center-ani">
            <img src="<?=img('about-intro.jpg')?>">
        </figure>

        <hgroup>
            <h2 class="no-heading-xl">
                <?= lang('main.intro.title') ?>
            </h2>
            <p class="no-body-lg">
                <?= lang('main.intro.desc') ?>
            </p>
        </hgroup>
    </div>
</section>

<section class="about-greet no-pd-120--y">
    <div class="no-container-xl">
        <h2 class="no-heading-md"><?= lang('greeting.title') ?></h2>
        <h3 class="no-heading-sm no-mg-32--t" <?= AOS_FADE_UP ?>><?= lang('greeting.summary') ?></h3>


        <div class="line" <?= AOS_FADE_UP ?>></div>

        <div class="f-wrap" <?= AOS_FADE_UP ?>>
            <!-- <figure>
                <img src="<?=img('profile.jpg')?>" class="pc">
                <img src="<?=img('profile-m.jpg')?>" class="mobile">
            </figure> -->

            <div class="txt">
                <p><?= lang('greeting.desc') ?></p>
				
				<div style="display: flex; align-items: center; justify-content: flex-end">
					<div style="max-width: 20rem;">
						<img src="<?=ROOT_URL?>/assets/img/sign.png" width="200" alt="권형기">
					</div>
				</div>
            </div>

        </div>
    </div>
</section>


<?php  endSection()  ?>
