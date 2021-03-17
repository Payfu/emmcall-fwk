<?php
  header('HTTP/1.0 404 Not Found');
?>
<div class="row">
  <div class="col-6 margin-top-1 mx-auto">
    <div class="card text-white bg-danger mb-3">
      <h2 class="card-header"><i class="fas fa-exclamation-triangle"></i> Erreur 404 !!!</h2>
      <div class="card-body">
        <h5 class="card-title">Cette page n'existe pas</h5>
        <p class="card-text"><i><?= $sessionValue;  ?></i></p>
      </div>
    </div>
  </div>
</div>



      

