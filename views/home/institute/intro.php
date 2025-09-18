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

<section class="about-center no-pd-120--y">
    <div class="no-container-xl">
        <div class="f-wrap">
            <!-- <figure <?= AOS_FADE_UP ?>>
                <img src="<?=img('about-center.jpg')?>" class="pc">
                <img src="<?=img('about-center-m.jpg')?>" class="mobile">
            </figure> -->

            <div>
                <h2 class="no-heading-md no-mg-60--b"><?= lang('intro.platform.title') ?></h2>
                <p class="no-body-xl"><?= lang('intro.platform.desc') ?></p>
            </div>
        </div>
    </div>
</section>

<?php  endSection()  ?>