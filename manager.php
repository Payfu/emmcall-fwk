<?php
require './config/env.php';

if(ENV === 'dev'){
?>
<!DOCTYPE html>
<html>
  
  <head>
    <meta charset="UTF-8">
    <title>Bundle Manager</title>
    <link rel="icon" type="image/png" href="favicon.png" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css" integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt" crossorigin="anonymous">
  </head>
  
  <body>
    <div class="container-fluid">
      <div class="row bg-dark mb-3">
        <div class="col-md-12 text-white text-center">
          <h1>Bundle manager</h1>
        </div>
      </div>
    </div>
    
    <div class="container">
      <!-- Message d'erreur -->
      <div class="row">
        <div class="col-12">
          <div class="alert alert-primary" id="alert-ok" role="alert"><i class='far fa-thumbs-up fa-2x'></i> <span id="msg-ok"></span></div>
          <div class="alert alert-danger" id="alert-ko" role="alert"><i class="fas fa-times fa-2x"></i> <span id="msg-ko"></span></div>
        </div>
      </div>
      
      <!-- Gestion des bundles -->
      <div class="row">
        <div class="col-md-6 my-3">
          <h3>Créer un bundle</h3>
          <form id="bundleForm">
            <div class="form-group">
              <input type="text" class="form-control" id="bundleName" placeholder="Nom du bundle" autocomplete="off">
            </div>
            <button type="submit" class="btn btn-primary">Créer</button>
          </form>
        </div>
        
        <!-- Gestion des entités -->
        <div class="col-md-6 my-3">
          <h3>Ajouter une entité</h3>
          <form id="entityForm">
            <div class="form-group">
              <input type="text" class="form-control" id="entityName" placeholder="Nom de la table cible" autocomplete="off">
            </div>
            <div class="form-group">
              <select class="form-control" id="listBundle">
              </select>
            </div>
            <div class="form-group">
              <select class="form-control" id="listBdd">
              </select>
            </div>
            <button type="submit" class="btn btn-primary">Ajouter</button>
          </form>
        </div>
      </div>
    </div>
    
    
  </body>
  <script  src="https://code.jquery.com/jquery-3.3.1.min.js"
  integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
  crossorigin="anonymous"></script>
  <script src="./core/Manager/manager.js"></script>
</html>
<?php } 
else{
  echo 'Permission refusée';
}
