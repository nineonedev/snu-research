<div class="team-nav no-mg-64--t">
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
                        <a href="<?= $subPage['path'] ?>" class="no-base-smenu no-pd-16--y <?= !empty($subPage['isActive']) ? 'active' : '' ?>">
                            <?= $subPage['title'] ?>
                        </a>
                    </li>
        <?php endforeach;
            }
        endforeach;
        ?>
    </ul>
</div>