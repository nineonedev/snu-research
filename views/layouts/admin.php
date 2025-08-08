<?php

use app\core\Config;

?>

<!DOCTYPE html>
<html lang="<?= Config::get('lang.current') ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>관리자</title>

    <!-- CSS ================================================== -->
    <!-- Fontawsome -->
    <link rel="stylesheet" href="<?=ROOT_URL?>/assets/lib/fontawesome/css/all.min.css" />

    <link rel="stylesheet" href="<?=ROOT_URL?>/assets/lib/aos/aos.min.css" />
    <link rel="stylesheet" href="<?=ROOT_URL?>/assets/lib/swiper/swiper.min.css" />

    <!-- JS ================================================== -->

    <!-- AOS -->
    <script src="<?=ROOT_URL?>/assets/lib/aos/aos.min.js"></script>

    <!-- jQuery -->
    <script src="<?=ROOT_URL?>/assets/lib/jquery/jquery.min.js"></script>
    <script src="<?=ROOT_URL?>/assets/lib/marquee/jquery.marquee.min.js"></script>

    <!-- Lenis -->
    <script src="<?=ROOT_URL?>/assets/lib/lenis/lenis.min.js"></script>

    <!-- Gsap -->
    <script src="<?=ROOT_URL?>/assets/lib/gsap/gsap.min.js"></script>
    <script src="<?=ROOT_URL?>/assets/lib/gsap/scrollTrigger.min.js"></script>
    <script src="<?=ROOT_URL?>/assets/lib/splitType/splitType.min.js"></script>

    <!-- Swiper -->
    <script src="<?=ROOT_URL?>/assets/lib/swiper/swiper.min.js"></script>

    <!-- Summernote -->
    <script src="<?=ROOT_URL?>/assets/lib/summernote/bootstrap.min.js"></script>
    <script src="<?=ROOT_URL?>/assets/lib/summernote/summernote.min.js"></script>
    

    <link rel="stylesheet" href="<?=ROOT_URL?>/assets/lib/summernote/bootstrap.min.css" />
    <link rel="stylesheet" href="<?=ROOT_URL?>/assets/lib/summernote/summernote.min.css" />
    <link rel="stylesheet" href="<?=ROOT_URL?>/assets/css/admin-build.css?v=<?=time()?>" />
    <?= $this->yield('style') ?>
</head>

<body>
    <div class="no-admin-wrap">
        <?= $this->include('admin.components.drawer') ?>
        
        <div class="no-admin-app">
            <?= $this->include('admin.components.header') ?>
            <main class="no-admin-content">
                <?= $this->yield('content') ?>
            </main>
        </div>
    </div>
    
    <div id="backdrop"></div>

    <script src="<?=ROOT_URL?>/assets/js/admin-build.js?v=<?=time()?>"></script>
    <?= $this->yield('script') ?>
</body>

</html>