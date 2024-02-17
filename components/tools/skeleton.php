<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Spin And Win | <?= page_title ?></title>
    <?php require "./components/tools/head.php"; ?>
    <?= custom_links ?>
</head>

<body>
    <div id="page">
        <?php
        require "components/tools/preloader.php";
        ?>

        <div class="container">
            <div class="page-content mt-3">
                <div class="min-vh-100">
                    <?= page_body ?>
                </div>
            </div>
        </div>

    </div>
    <?php require "./components/tools/scripts.php"; ?>
    <?= custom_scripts ?>
</body>

</html>