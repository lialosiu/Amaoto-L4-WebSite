<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

    <title><?= Config::get('constants.site-name') ?></title>

    <!-- jQuery -->
    <script type="text/javascript" src="<?= URL::to('packages/jquery-2.1.0/jquery.js') ?>"></script>

    <!-- jQuery UI -->
    <script type="text/javascript" src="<?= URL::to('packages/jquery-ui.min.js') ?>"></script>

    <!-- jQuery Cookie -->
    <script type="text/javascript" src="<?= URL::to('packages/jquery.cookie-1.4.1.min.js') ?>"></script>

    <!-- Bootstrap -->
    <link rel="stylesheet" type="text/css" href="<?= URL::to('packages/bootstrap-3.1.1-dist/css/bootstrap.min.css') ?>">
    <script type="text/javascript" src="<?= URL::to('packages/bootstrap-3.1.1-dist/js/bootstrap.min.js') ?>"></script>

    <!-- Bootstrap Validator -->
    <script type="text/javascript" src="<?= URL::to('packages/bootstrap-validator.min.js') ?>"></script>

    <!-- Bootstrap Slider -->
    <link rel="stylesheet" type="text/css" href="<?= URL::to('packages/bootstrap-slider-dist/css/bootstrap-slider.min.css') ?>">
    <script type="text/javascript" src="<?= URL::to('packages/bootstrap-slider-dist/bootstrap-slider.min.js') ?>"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" type="text/css" href="<?= URL::to('packages/font-awesome-4.0.3/css/font-awesome.min.css') ?>">

    <!-- SparkMD5 -->
    <script type="text/javascript" src="<?= URL::to('packages/spark-md5/spark-md5.js') ?>"></script>

    <!-- PlUpload -->
    <script type="text/javascript" src="<?= URL::to('packages/plupload-2.1.1/plupload.full.min.js') ?>"></script>
    <script type="text/javascript" src="<?= URL::to('packages/plupload-2.1.1/i18n/zh_CN.js') ?>"></script>

    <!-- Noty -->
    <script type="text/javascript" src="<?= URL::to('packages/noty-2.2.2/packaged/jquery.noty.packaged.min.js') ?>"></script>
    <script type="text/javascript" src="<?= URL::to('packages/noty-2.2.2/themes/default.js') ?>"></script>
    <script type="text/javascript" src="<?= URL::to('packages/noty-2.2.2/options.js') ?>"></script>

    <!-- jPlayer -->
    <script type="text/javascript" src="<?= URL::to('packages/jQuery.jPlayer.2.6.0/jquery.jplayer.min.js') ?>"></script>

    <!-- Dancer.js -->
    <!--    <script type="text/javascript" src="--><? //= URL::to('packages/dancer.js-0.4.0/dancer.js') ?><!--"></script>-->

    <!-- Amaoto -->
    <link rel="stylesheet" type="text/css" href="<?= URL::to('packages/amaoto/amaoto.css') ?>?v=<?= filemtime(public_path('packages/amaoto/amaoto.css')) ?>">
    <script type="text/javascript" src="<?= URL::to('packages/amaoto/amaoto.js') ?>?v=<?= filemtime(public_path('packages/amaoto/amaoto.js')) ?>"></script>

    <script>
        BaseUrl = '<?= URL::to('/') ?>';

        $(document).ready(function () {
            $('body').fadeIn('slow');
        });
    </script>

    @yield('html-head')

</head>
<body style="display:none; background-image: url(<?= URL::to('packages/amaoto/images/bg_b.gif') ?>)">
@yield('html-body')
</body>
</html>
