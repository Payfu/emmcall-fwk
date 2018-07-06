<?php

// On récupère la liste des bundles du dossier src
$dir    = '../../app/src';
$files = scandir($dir);
$list = "<option disabled selected>Sélectionnez le bundle</option>";
foreach ($files as $v){
  if (preg_match("/[Bundle]$/", $v)) {
    $list .= "<option value='{$v}'>{$v}</option>";
  } 
}
$data = ["type"=>"listBundle", "list"=>$list];
echo json_encode($data);








