<!DOCTYPE html>
<html lang="<?= App::getInstance()->lang; ?>">
  <head>
    <meta charset="utf-8">
    <title><?= $metaTitle ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= $metaDescription ?>">
    <meta name="author" content="<?= App::getInstance()->author; ?>">
    <link rel="icon" type="image/png" href="picture/favicon.png" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">

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
          <strong><?= App::getInstance()->copyright; ?></strong>
        </footer>
      </div>
      
    </div>

    <!-- js scripts -->    
    <?php if(isset($scripts_js)){ echo $scripts_js; } ?>

    <!-- Google analytics -->
  </body>
</html>

