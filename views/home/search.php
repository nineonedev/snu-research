<?php

use app\core\Config;

 section('content') ?>

<form >
<section class="no-sub-visual no-pd-64--b">
        <div class="no-container-xl">
            <hgroup>
                <h2 class="no-heading-xl no-mg-16--b">
                Search
                </h2>
            </hgroup>
        </div>
    </section>

<section class="sub-active no-pd-120--y">
    <div class="no-container-xl">
        
        <div class="no-pd-36--t"></div>
        <div class="opt_top">
            <ul class="num_info">
                <li><b class="no-body-lg"><?=$total?></b> results</li>
            </ul>

            <div class="search-box">
                <input type="query" name="query" placeholder="Search" value="<?=$query?>" class="no-body-md">
                <button type="submit">
                    <i class="fa-light fa-magnifying-glass"></i>
                </button>
            </div>
        </div>
        
        <?php if($results) : ?>
        <ul class="active-list no-pd-48--t" <?= AOS_FADE_UP ?>>
            <?php foreach ($results as $post) : ?>
            <li>
                <a href="<?=$post['path']?>">
                    <figure>
                        <img src="<?= $post['image']?>">
                    </figure>
                    <div class="txt no-pd-24--y">
                        <h3 class="no-heading-xs no-mg-8--b"><?=$post['title']?></h3>
                        <p class="no-body-lg no-mg-16--b">
                            <?= htmlspecialchars(mb_strimwidth(strip_tags($post['content'] ?? ''), 0, 200, '...'))?>
                        </p>
                        <span class="no-body-lg">2025-02-15</span>
                    </div>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php else: ?>

        <p class="no-results-notfound"><?= lang('search.notfound') ?></p>
        <?php endif; ?>


        <div class="no-mg-64--t">
            <?= $pagination ?>
        </div>
    </div>

</section>
</form>

<?php endSection() ?>
