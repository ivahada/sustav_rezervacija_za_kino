<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Throwback Cinema</title>
    
    <!--JQUERY & BOOTSTRAP IMPORT -->
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

	<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    
    <!--USED FOR CAROUSEL
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>-->

    

    <!--OTHER CSS IMPORT -->
    <link rel="stylesheet" type="text/css" href="css/sidebar.css"/>
    <link rel="stylesheet" type="text/css" href="css/style.css"/>


		

</head>
<body>

<!--ENTER NAVIGATION BAR HERE-->
<?php include __DIR__ . '/nav_bar.php'; ?>    

<!--full width container-->
<div class="container-fluid" >
  
  <!--HAS MAIN AND SIDE_BAR -->
  <div class="row" >

      <!--ENTER SIDE BAR HERE-->
      <?php include __DIR__ . '/'.$USERTYPE.'/side_bar.php'; ?>   

      <main  class="col-md-9 ml-sm-auto col-lg-10 px-md-4 py-4 glavni">