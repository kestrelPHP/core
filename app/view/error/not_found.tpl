<!DOCTYPE html>
<!--[if IE]><![endif]-->
<!--[if IE 8 ]>
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" class="ie8"><![endif]-->
<!--[if IE 9 ]>
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" class="ie9"><![endif]-->
<!--[if (gt IE 9)|!(IE)]><!-->
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>">
<!--<![endif]-->
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <?php if ($title) { ?><title><?php echo $title; ?></title> <?php } ?>
    <?php if ($base) { ?><base href="<?php echo $base; ?>" /><?php } ?>
    <?php if ($description) { ?><meta name="description" content="<?php echo $description; ?>"/><?php } ?>
    <?php if ($keywords) { ?><meta name="keywords" content="<?php echo $keywords; ?>"/><?php } ?>

    <link href='http://fonts.googleapis.com/css?family=Yanone+Kaffeesatz:400,700,300,200' rel='stylesheet'
          type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=PT+Sans:400,700,400italic,700italic' rel='stylesheet'
          type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Sansita+One' rel='stylesheet' type='text/css'>

    <?php foreach ($styles as $style) { ?><link href="<?php echo $style['href']; ?>" type="text/css" rel="<?php echo $style['rel']; ?>"
                                                media="<?php echo $style['media']; ?>"/>
    <?php } ?>

    <?php foreach ($links as $link) { ?><link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>"/>
    <?php } ?>

    <script src="app/view/javascript/jquery/jquery-2.1.1.min.js" type="text/javascript"></script>
    <script src="app/view/javascript/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <style type="text/css">
        * {
            margin: 0px;
            padding: 0px;
        }

        body {
            background: #000000;
            overflow: auto;
        }

        h1, h3, h4 {
            font-family: 'Yanone Kaffeesatz', sans-serif;
            font-weight: normal;
        }

        h1 {
            color: #fff;
            font-size: 200px;
        }

        h3 {
            color: #facb00;
            font-size: 72px;
        }

        h4 {
            color: #cca2d8;
            width: 80%;
            font-size: 36px;
            margin-bottom: 25px;
        }

        .left-error-content, .right-error-content {
            margin-top: 5%;
            margin-bottom: 5%;
        }

        .right-error-content {
            position: relative;
            z-index: 9999;
        }

        #shell {
            z-index: 1;
            position: absolute;
            width: 53%;
            display: block;
            margin-left: -85px;
        }

        img#wall {
            position: absolute;
            z-index: 2;
        }

        img#shadow {
            position: absolute;
            z-index: 3;
        }

        img#ega {
            position: absolute;
            z-index: 4;
        }

        img#lamp {
            position: absolute;
            z-index: 5;
        }

        img#table {
            position: absolute;
            z-index: 6;
        }

        img#adit {
            position: absolute;
            z-index: 7;
        }

        img#hanif {
            position: absolute;
            z-index: 8;
        }

        img#door {
            position: absolute;
            z-index: 9;
        }

        a.button {
            display: table;
            margin-bottom: 25px;
        }
    </style>
    <script type="text/javascript">
        $(document).ready(function () {
            $('#shell img').plaxify()
            $.plax.enable()
        })
    </script>
</head>

<body>
<div class="container">

    <div class="row">

        <div class="span6 left-error-content">
            <div id="shell">
                <img src="<?php echo $img_location; ?>img/01wall.png" data-xrange="40" data-yrange="0" id="wall">
                <img src="<?php echo $img_location; ?>img/02shadow.png" data-xrange="30" data-yrange="0" id="shadow">
                <img src="<?php echo $img_location; ?>img/03ega.png" data-xrange="20" data-yrange="0" id="ega">
                <img src="<?php echo $img_location; ?>img/04lamp.png" data-xrange="10" data-yrange="0" id="lamp">
                <img src="<?php echo $img_location; ?>img/05table.png" data-xrange="5" data-yrange="0" id="table">
                <img src="<?php echo $img_location; ?>img/06adit.png" data-xrange="10" data-yrange="0"
                     data-invert="true" id="adit">
                <img src="<?php echo $img_location; ?>img/07hanif.png" data-xrange="15" data-yrange="0"
                     data-invert="true" id="hanif">
                <img src="<?php echo $img_location; ?>img/08door.png" data-xrange="0" data-yrange="0" data-invert="true"
                     id="door">
            </div>
        </div>

        <div class="span6 right-error-content">

            <h1>404</h1>
            <h3>Ooooooopps!</h3>
            <h4><?php echo $lang_text_error;?></h4>
            <a href="<?php echo $link_home ;?>" class="button red-button"> <?php echo $lang_home ;?> </a>
            <a href="https://facebook.com/skumbaz" target="blank" class="button blue-button"> <?php echo $button_or_tweet ;?> </a>
        </div>

    </div>

</div>
</body>
</html

