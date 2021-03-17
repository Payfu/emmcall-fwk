<!DOCTYPE html>
<html lang="<?= App::getInstance()->lang; ?>">
  <head>
    <meta charset="utf-8">
    <title><?= $metaTitle ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= $metaDescription ?>">
    <meta name="author" content="<?= App::getInstance()->author; ?>">
    <link rel="icon" type="image/png" href="icons/favicon-128.png" />       
    
    <!--Boostrap-->
    <link rel="stylesheet" href="scripts/css/bootstrap/bootstrap.css?<?php echo rand();?>">
    
    <!--JQuery Confirm-->
    <link rel="stylesheet" href="scripts/css/jquery-confirm/jquery-confirm.css?<?php echo rand();?>"/>

    <!-- Le css styles --> 
    <?php if(isset($scripts_css)){ echo $scripts_css; } ?>
    
  </head>
  <body>
 
    <!-- Nav Bar -->
		
    <!-- container -->
    <div class="container">
        <?= $content; ?>
      
      <!-- Footer -->
      <div class="row">
        <footer class="section-footer col-12 text-center">
          <strong><?= date("Y")?> <?= App::getInstance()->copyright; ?></strong>
        </footer>
      </div>
      
    </div>
    
    <!--JQuery-->
    <script src="<?php echo WEBROOT;?>/scripts/js/jquery/jquery.js?<?php echo rand();?>"></script>
    
    <!--Jquery confirm-->
    <script src="<?php echo WEBROOT;?>/scripts/js/jquery-confirm/jquery-confirm.js?<?php echo rand();?>"></script>
    
    <!-- js scripts -->    
    <?php if(isset($scripts_js)){ echo $scripts_js; } ?>
    
  </body>
</html>