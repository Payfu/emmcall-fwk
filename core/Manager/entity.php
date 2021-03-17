<?php
ini_set('display_errors', '1');
// On appel tous les fichiers contenant des classe.
require_once './../../config/env.php';
require_once './Autoloader.php';

//use Core\Config;

if(ENV === 'dev'){
  
  // On récupère le nom de la table et le nom du bundle
  if(
    isset($_POST['action']) 
    && $_POST['action'] === 'create' 
    && filter_input(INPUT_POST, 'entityName', FILTER_SANITIZE_FULL_SPECIAL_CHARS) 
    && filter_input(INPUT_POST, 'bddName', FILTER_SANITIZE_FULL_SPECIAL_CHARS)
  ){
  
    $gs = new GetSetController($_POST['entityName'], $_POST['bddName']);
    
    if(!$gs->isTableExist()){
      $data = ["type"=>"ko", "msg" => "La table <strong>{$_POST['entityName']}</strong> n'existe pas dans la base de données." ];
      echo json_encode($data);
      exit();
    }
    
    // Formate le_nom_de_la_table en LeNomDeLaTable et on garde les deux valeurs.
    $nomTableUnformat = $_POST['entityName'];
    $nomTableFormat   = str_replace(' ', '', ucwords(str_replace('_', " ", strtolower($nomTableUnformat))));
    
    // On verifie que le bundle existe
    $dirPath        = "../../App/Entities";
    $dirPathTable   = "{$dirPath}/Tables";    
    $dirPathObject  = "{$dirPath}/Objects";
    $fileTable      = $dirPathTable . "/{$nomTableFormat}Table.php";
    $fileObject     = $dirPathObject . "/{$nomTableFormat}Obj.php";
    $fileTableDemo  = './demo/tableDemo.txt';
    $fileObjectDemo = './demo/objectDemo.txt';
    
    // Si le bundle existe mais pas les dossier Entity et Table
    if(is_dir($dirPath) && !is_file($fileTable) && !is_file($fileObject)){
      
      $valid = true;
      // On récupère tableDemo.txt, entityDemo.txt et objectDome.txt
      $demoTable  = file_get_contents($fileTableDemo);
      $demoObject = file_get_contents($fileObjectDemo);
      
      // On récupère les getters et setters
      $getSet = $gs->create();
      
      // On remplace la chaîne %bundleName% par $nomBundle
      $newDemoFileObject  = str_replace(['%objectName%', '%gettersSetters%'], [$nomTableFormat, $getSet], $demoObject);
      $newDemoFileTable  = str_replace(['%tableName%', '%tableNameUnformat%'], [$nomTableFormat, $nomTableUnformat], $demoTable);
      
      // On crée les dossiers (s'ils n'existe pas) avec le nom de la table et de l'object dans le dossier /App/Entities/[Objects / Tables]
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
      
      if(is_dir($dirPathObject)){
        $newObject = fopen($fileObject, 'w+');
        fwrite($newObject, $newDemoFileObject);
        fclose($newObject);
      } elseif(mkdir($dirPathObject, 0644, true)){        
        $newObject = fopen($fileObject, 'w+');
        fwrite($newObject, $newDemoFileObject);
        fclose($newObject);
      } else {
        $valid = false;
      }
      
      if($valid){
        $data = ["type"=>"ok", "msg" => "L'entité <strong>{$nomTableFormat}</strong> est correctement créée." ];
      }
    } 
    else {
      $data = ["type"=>"ko", "msg" => "L'entitié <strong>{$nomTableFormat}</strong> existe déjà." ];
    }    
    echo json_encode($data);
  } 
}
