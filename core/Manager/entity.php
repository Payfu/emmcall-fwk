<?php
require_once './../../config/env.php';

if(ENV === 'dev'){
  
  // On récupère le nom de la table et le nom du bundle
  if(isset($_POST['action']) && $_POST['action'] === 'create' && filter_input(INPUT_POST, 'entityName', FILTER_SANITIZE_FULL_SPECIAL_CHARS) && filter_input(INPUT_POST, 'listBundle', FILTER_SANITIZE_FULL_SPECIAL_CHARS)){
    
    // Formate le_nom_de_la_table en LeNomDeLaTable et on garde les deux valeurs.
    $nomTableUnformat = $_POST['entityName'];
    $nomTableFormat   = str_replace(' ', '', ucwords(str_replace('_', " ", $nomTableUnformat)));
    
    $nomBundle = $_POST['listBundle'];
    
    // On verifie que le bundle existe
    $dirPath        = "../../app/src/{$nomBundle}";
    $dirPathEntity  = "../../app/src/{$nomBundle}/Entity";
    $dirPathTable   = "../../app/src/{$nomBundle}/Table";
    $fileTable      = $dirPathTable . "/{$nomTableFormat}Table.php";
    $fileEntity     = $dirPathEntity . "/{$nomTableFormat}Entity.php";
    $fileTableDemo  = './demo/tableDemo.txt';
    $fileEntityDemo = './demo/entityDemo.txt';
    
    // Si le bundle existe mais pas les dossier Entity et Table
    if(is_dir($dirPath) && !is_file($fileEntity) && !is_file($fileTable)){
      
      
      $valid = true;
      // On récupère tableDemo.txt et entityDemo.txt
      $demoTable  = file_get_contents($fileTableDemo);
      $demoEntity = file_get_contents($fileEntityDemo);
      
      
      // On remplace la chaîne %bundleName% par $nomBundle
      $newDemoFileTable  = str_replace(['%bundleName%', '%tableName%', '%tableNameUnformat%'], [$nomBundle, $nomTableFormat, $nomTableUnformat], $demoTable);
      $newDemoFileEntity = str_replace(['%bundleName%', '%entityName%'], [$nomBundle, $nomTableFormat], $demoEntity);
      
      // On crée les dossiers (s'ils n'existe pas) avec le nom de  la table et de l'entity dans le dossier /src/nomBundle
      if(is_dir($dirPathTable)){
        $newTable = fopen($fileTable, 'w+');        
        fwrite($newTable, $newDemoFileTable);
        fclose($newTable);
      } elseif(mkdir($dirPathTable, 0644, true)){        
        $newTable = fopen($fileTable, 'w+');        
        fwrite($newTable, $newDemoFileTable);
        fclose($newTable);
      } else {
        $valid = false;
      }
      
      if(is_dir($dirPathEntity)){
        $newEntity = fopen($fileEntity, 'w+');
        fwrite($newEntity, $newDemoFileEntity);
        fclose($newEntity);
      } elseif(mkdir($dirPathEntity, 0644, true)){        
        $newEntity = fopen($fileEntity, 'w+');
        fwrite($newEntity, $newDemoFileEntity);
        fclose($newEntity);
      } else {
        $valid = false;
      }
      
      if($valid){
        $data = ["type"=>"ok", "msg" => "L'entité <strong>{$nomTableFormat}</strong> est correctement créée pour le bundle <strong>{$nomBundle}</strong>." ];
      } 
    } else {
      $data = ["type"=>"ko", "msg" => "L'entitié <strong>{$nomTableFormat}</strong> existe déjà dans le bundle <strong>{$nomBundle}</strong>." ];
    }
    
    echo json_encode($data);
  } 
  
  
  
  
  
}

