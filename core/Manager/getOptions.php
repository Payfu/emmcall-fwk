<?php
/*
 * On récupère la liste des clefs de connexion bdd
 * Le substring efface core/Manager
 */
define('ROOT', dirname(__FILE__));
$arrayConfig  =  yaml_parse_file(substr(ROOT, 0, -12) . 'config/config.yml')['database'];
$listKeys = array_keys($arrayConfig);
$listClef = "<option disabled selected>Sélectionnez la base de données</option>";
foreach ($listKeys as $v){
  $listClef .= "<option value='{$v}'>{$v}</option>";
}

/*
 *  On récupère la liste des bundles du dossier src
 */
$dir    = '../../app/src';
$files = scandir($dir);
$list = "<option disabled selected>Sélectionnez le bundle</option>";
foreach ($files as $v){
  if (preg_match("/[Bundle]$/", $v)) {
    $list .= "<option value='{$v}'>{$v}</option>";
  } 
}
$data = ["type"=>"listBundle", "list"=>$list, "bddKeys"=>$listClef];
echo json_encode($data);