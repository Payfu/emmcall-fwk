<!DOCTYPE html>
<html lang="<?= App::getInstance()->lang; ?>">
  <head>
    <meta charset="utf-8">
    <title><?= $metaTitle ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= $metaDescription ?>">
    <meta name="author" content="<?= App::getInstance()->author; ?>">
    <link rel="icon" type="image/png" href="picture/favicon.png" />       
    
    <!--Jquery UI-->
    <link rel="stylesheet" href="scripts/css/jquery/jquery-ui-1.12.0.css?<?php echo rand();?>"/>
    
    <!--Boostrap-->
    <link rel="stylesheet" href="scripts/css/bootstrap/bootstrap-4.3.1.min.css?<?php echo rand();?>">
    
    <!--fontawesome-->
    <link rel="stylesheet" href="scripts/css/fontawesome/all.min.css?<?php echo rand();?>">
    
    <!--Fancy box-->
    <link rel="stylesheet" href="scripts/css/jquery/jquery.fancybox.min.css?<?php echo rand();?>"/>
    
    <!--JQuery Confirm-->
    <link rel="stylesheet" href="scripts/css/jquery/jquery-confirm.css?<?php echo rand();?>"/>

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
    <script src="<?php echo WEBROOT;?>/scripts/js/jquery/jquery-3.4.1.min.js?<?php echo rand();?>"></script>
    <script src="<?php echo WEBROOT;?>/scripts/js/jquery/jquery-ui-1.10.0.min.js?<?php echo rand();?>"></script>
    
    <!--Fancybox-->
    <script src="<?php echo WEBROOT;?>/scripts/js/jquery/jquery.fancybox.min.js?<?php echo rand();?>"></script>
    
    <!--Jquery confirm-->
    <script src="<?php echo WEBROOT;?>/scripts/js/jquery/jquery-confirm.js?<?php echo rand();?>"></script>
    
    <!--Popper-->
    <script src="<?php echo WEBROOT;?>/scripts/js/bootstrap/popper-1.14.7.min.js?<?php echo rand();?>"></script>
    
    <!--Boostrap-->
    <script src="<?php echo WEBROOT;?>/scripts/js/bootstrap/bootstrap-4.3.1.min.js?<?php echo rand();?>"></script>        
    
    <!-- js scripts -->    
    <?php if(isset($scripts_js)){ echo $scripts_js; } ?>
    
  </body>
</html>
