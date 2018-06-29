<!DOCTYPE html>
<html lang="<?= App::getInstance()->lang; ?>">
  <head>
    <meta charset="utf-8">
    <title><?= $metaTitle ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= $metaDescription ?>">
    <meta name="author" content="<?= App::getInstance()->author; ?>">
    <link rel="icon" type="image/png" href="favicon.png" />

    <!-- Le css styles --> 
    <?php if(isset($scripts_css)){ echo $scripts_css; } ?>
    
  </head>
  
  
  <body>
 
    <!-- Nav Bar -->
		
    <!-- container -->
    <div class="container-fluid">
        <?= $content; ?>
	    	    
      <!-- Footer -->
      <footer class="section-footer">
        <p><strong><?= App::getInstance()->copyright; ?></strong></p>
      </footer>
    </div>

    <!-- js scripts -->    
		<?php if(isset($scripts_js)){ echo $scripts_js; } ?>

    <!-- Google analytics -->
  </body>
</html>

