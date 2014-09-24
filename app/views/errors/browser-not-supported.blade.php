<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

    <title><?= Config::get('constants.site-name') ?></title>

    <link rel="stylesheet" type="text/css" href="<?= URL::to('packages/bootstrap-3.1.1-dist/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= URL::to('packages/amaoto/amaoto.css') ?>?v=<?= filemtime(public_path('packages/amaoto/amaoto.css')) ?>">

    <style>

    </style>
</head>
<body style="background-image: url(<?= URL::to('packages/amaoto/images/bg_b.gif') ?>)">
<div style="margin: 100px auto;">
    <div class="text-center">
        <h2 style="padding-bottom: 20px; margin: 0 100px; border-bottom: 1px dashed #888"><?= Config::get('constants.site-name') ?> - Error Page</h2>

        <br/>

        <h1 class="text-danger">检测到客户端浏览器不兼容</h1>

        <p class="text-warning"><?= Request::header('user-agent') ?></p>

        <h3 class="text-primary">请使用支持 HTML5/CSS3 等高级特性的浏览器进行访问</h3>

        <br/>
        <br/>

        <p>……嘛，如果是360啊或者什么猎豹等国产壳牌浏览器，切换成极速模式就应该可以了。</p>

        <p>不过还是强烈推荐使用 Firefox， Chrome， IE11 等现代浏览器。</p>
    </div>
</div>
<div id="site-footer">
    <span>Copyright &copy; <?= Config::get('constants.copyright-year') ?> <?= Config::get('constants.copyright-name') ?>. All rights reserved. Powered by Lialosiu. Version <?= Config::get('constants.version') ?>.</span>
</div>
</body>
</html>