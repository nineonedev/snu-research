<section class="no-sub-nav-menu no-container-xl no-mg-60--t no-mg-40--b">
    <div class="nav-menu-slider">
        <ul class="swiper-wrapper">
            <?php
            $hasActiveThirdDepth = false;

            foreach ($CUR_PAGE_LIST[0]['pages'] as $v) {
                if (!empty($v['isActive']) && !empty($v['pages'])) {
                    $hasActiveThirdDepth = true;
                    break;
                }
            }

            foreach ($CUR_PAGE_LIST[0]['pages'] as $v) :
                $isActive = !empty($v['isActive']);
                $hasThirdDepth = !empty($v['pages']);

                if ($hasActiveThirdDepth && $isActive && $hasThirdDepth) {
                    foreach ($v['pages'] as $subPage) : ?>
                        <li class="swiper-slide">
                            <a href="<?= $subPage['path'] ?>" class="no-body-md <?= !empty($subPage['isActive']) ? 'active' : '' ?>">
                                <?= $subPage['title'] ?>
                            </a>
                        </li>
            <?php endforeach;
                }
            endforeach;
            ?>
        </ul>
    </div>
</section>