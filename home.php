<!DOCTYPE HTML>
<html>
  <head lang="en-US">
    <title>SMT CRITICAL PART</title>

    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">

    <link rel="stylesheet" type="text/css" href="../extjs-5.1.1/packages/ext-theme-neptune/build/resources/ext-theme-neptune-all.css">
    <script type="text/javascript" src="../extjs-5.1.1/build/ext-all.js"></script>
    <script type="text/javascript" src="../extjs-5.1.1/packages/ext-theme-neptune/build/ext-theme-neptune.js"></script>

    <meta http-equiv="X-UA-compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="resources/favicon.ico">
<!-- 
    <link rel="stylesheet" href="../bootstrap/3.3.6/css/bootstrap.min.css"/>
    <link rel="stylesheet" type="text/css" href="asset/css/style.css"/>
    <link rel="stylesheet" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css"/> -->

    <!-- <script src="../bootstrap/jquery/jquery-1.12.0.min.js"></script> -->
    <!-- <script src="../bootstrap/js/bootstrap.min.js"></script> -->
    <!-- <script type="text/javascript">
      $(document).ready(function(){
        $("#menu-toggle").click(function(e) {
          e.preventDefault();
          $("#wrapper").toggleClass("toggled");
        });
      });
    </script> -->
    <style type="text/css">
    @font-face {
      font-family: 'Roboto Condensed';
      font-style: normal;
      font-weight: 400;
      src: url('../../font/RobotoCondensed-Regular.ttf');
    }

    * {
      font-family: 'Roboto Condensed', sans-serif;
      text-decoration: none !important;
      outline: 0 !important;
    }
    .customFieldSet {
      border: 1px solid black;
    }
    .customFieldSet legend {
      text-align: center;
    }
    .customLabel label {
      text-align: center;
      font-weight: bold;
      font-size: 12px;
    }
    /*[class*=x-form-text] {
      font-family: 'Roboto Condensed', sans-serif;
    }*/
    </style>
  </head>
  <body>
    <!-- <?php include_once('view/navigation.php'); ?> -->
    <section>
			<div class="section-body">
        <?php
          if (!empty($_REQUEST['view'])){
            $dir = 'view';
            $checking = scandir($dir);
            unset($checking[0], $checking[1]);

            $page = $_REQUEST['view'];
            if (in_array($page.'.php', $checking)) {
              include_once('view/'.$page.'.php');
            } else {
              echo 'error';
            }
          }
        ?>
			</div>
    </section>
  </body>
</html>