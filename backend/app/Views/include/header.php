<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

?>


<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Registration</title>
    <meta name="author" content="themeholy">
    <meta name="description" content="IAOI">
    <meta name="keywords" content="IAOI">
    <meta name="robots" content="INDEX,FOLLOW">
    <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
    <!-- <link rel="apple-touch-icon" sizes="57x57" href="<?php echo BASEURL ?>assets\img\IAOI Logo-1.jpg">
    <link rel="apple-touch-icon" sizes="60x60" href="<?php echo BASEURL ?>assets\img\IAOI Logo-1.jpg">
    <link rel="apple-touch-icon" sizes="72x72" href="<?php echo BASEURL ?>assets\img\IAOI Logo-1.jpg">
    <link rel="apple-touch-icon" sizes="76x76" href="<?php echo BASEURL ?>assets\img\IAOI Logo-1.jpg">
    <link rel="apple-touch-icon" sizes="114x114" href="<?php echo BASEURL ?>assets\img\IAOI Logo-1.jpg">
    <link rel="apple-touch-icon" sizes="120x120" href="<?php echo BASEURL ?>assets\img\IAOI Logo-1.jpg">
    <link rel="apple-touch-icon" sizes="144x144" href="<?php echo BASEURL ?>assets\img\IAOI Logo-1.jpg">
    <link rel="apple-touch-icon" sizes="152x152" href="<?php echo BASEURL ?>assets\img\IAOI Logo-1.jpg">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo BASEURL ?>assets\img\IAOI Logo-1.jpg">
    <link rel="icon" type="image/png" sizes="192x192" href="<?php echo BASEURL ?>assets\img\IAOI Logo-1.jpg">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo BASEURL ?>assets\img\IAOI Logo-1.jpg">
    <link rel="icon" type="image/png" sizes="96x96" href="<?php echo BASEURL ?>assets\img\IAOI Logo-1.jpg">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo BASEURL ?>assets\img\IAOI Logo-1.jpg"> -->
    <!-- <link rel="manifest" href="assets/img/favicons/manifest.html">-->
    <meta name="msapplication-TileColor" content="#ffffff">
    <!-- <meta name="msapplication-TileImage" content="<?php echo BASEURL ?>assets\img\IAOI Logo-1.jpg"> -->
    <meta name="theme-color" content="#ffffff">
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400;500;600;700;800&amp;family=Jost:wght@300;400;500;600;700;800;900&amp;family=Roboto:wght@100;300;400;500;700&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASEURL ?>assets/css/app.min.css">
    <link rel="stylesheet" href="<?php echo BASEURL ?>assets/css/fontawesome.min.css">
    <link rel="stylesheet" href="<?php echo BASEURL ?>assets/css/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>

<body>

    <div class="popup-search-box d-none d-lg-block"><button class="searchClose"><i class="fal fa-times"></i></button>
        <form action="#"><input type="text" placeholder="What are you looking for?"> <button type="submit"><i class="fal fa-search"></i></button>
        </form>
    </div>



    <script>


        function scrollFunction() {
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                document.getElementById("navbar").style.top = "0";
            } else {
                document.getElementById("navbar").style.top = "";
            }
        }
        window.addEventListener('load', function() {
            var loader = document.querySelector('.loader');
            loader.style.display = 'none';
        });
    </script>