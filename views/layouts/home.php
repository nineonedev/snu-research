<?php

use app\core\Config;
use app\facades\DB;
use app\models\Setting;

$setting = Setting::all();
$setting = $setting ? $setting[0] : [];

$lang = $setting 
        ?  DB::table('no_setting_langs')
            ->where('setting_id', '=', $setting->id)
            ->where('locale', '=', Config::get('locale'))
            ->first()
        : [];
Config::set('setting', $lang);

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$currentUrl = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

?>

<!DOCTYPE html>
<html lang="<?= Config::get('locale') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=$lang['site_name'] . ' | ' . $lang['meta_title']?></title>
    <meta name="description" content="<?=$lang['meta_description']?>">
    <meta name="keywords" content="<?=$lang['meta_keywords']?>">
	<meta name="robots" content="index, follow">

	<meta property="og:title" content="<?=$lang['meta_title']?>">
	<meta property="og:description" content="<?=$lang['meta_description']?>">
	<meta property="og:type" content="website">
	<meta property="og:url" content="<?=$currentUrl?>">
	<meta property="og:image" content="<?=ROOT_URL?>/assets/img/default.jpg">

	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:title" content="<?=$lang['meta_title']?>">
	<meta name="twitter:description" content="<?=$lang['meta_description']?>">
	<meta name="twitter:image" content="<?=ROOT_URL?>/assets/img/default.jpg">

	<link rel="canonical" href="<?=$currentUrl?>">


    <link rel="apple-touch-icon" sizes="180x180" href="<?=ROOT_URL?>/assets/img/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?=ROOT_URL?>/assets/img/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?=ROOT_URL?>/assets/img/favicon/favicon-16x16.png">
    <link rel="manifest" href="<?=ROOT_URL?>/assets/img/favicon/site.webmanifest">

    <!-- CSS ================================================== -->
    <!-- Fontawsome -->
    <link rel="stylesheet" href="<?=ROOT_URL?>/assets/lib/fontawesome/css/all.min.css" />
    <link rel="stylesheet" href="<?=ROOT_URL?>/assets/lib/aos/aos.min.css" />
    <link rel="stylesheet" href="<?=ROOT_URL?>/assets/lib/swiper/swiper.min.css" />
    
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
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

    <link rel="stylesheet" href="<?=ROOT_URL?>/assets/css/build.css?v=<?=time()?>" />
    <?= $this->yield('style') ?>
</head>
<body>
    <?= $this->include('home.components.header') ?>
    
    <main><?= $this->yield('content') ?></main>

    <?= $this->include('home.components.footer') ?>

    <script src="<?=ROOT_URL?>/assets/js/build.js?v=<?=time()?>"></script>
    <?= $this->yield('script') ?>
</body>
</html>