<?php
require_once './../../config/env.php';

if(ENV === 'dev'){
  
  // creation d'un bundle
  if(isset($_POST['action']) && $_POST['action'] === 'create' && isset($_POST['bundleName'])){
    
    $nomFormatBundle = ucfirst(strtolower($_POST['bundleName']));
    
    // Si le Bundle n'existe pas on le crée
    $dirPath            = "../../App/Src/{$nomFormatBundle}Bundle/Controller";
    $dirPathView        = "../../App/Src/{$nomFormatBundle}Bundle/Views/";
    $dirPathPublic      = "../../App/Src/{$nomFormatBundle}Bundle/Public/";
    $fileController     = $dirPath . "/{$nomFormatBundle}Controller.php";
    $fileControllerDemo = './demo/controllerDemo.txt';
    
    if(!is_dir($dirPath) && !is_dir($dirPathView)){
      $valid = true;
      // Création du contrôleur 
      // On récupère controllerDemo.txt
      $demoFile = file_get_contents($fileControllerDemo);
      // On remplace la chaîne %name% par $nomFormatBundle
      $newDemoFile = str_replace('%name%', $nomFormatBundle, $demoFile);
      // On crée un dossier avec le nom du bundle dans le dossier /src
      if(mkdir($dirPath, 0644, true)){        
        $new = fopen($fileController, 'w+');
        fwrite($new, $newDemoFile);
        fclose($new);
      } else {
        $valid = false;
      }
      
      // Création de la vue
      if(mkdir($dirPathView, 0644, true)){        
        $newView = fopen($dirPathView . '/index.php', 'w+');
        fwrite($newView, "Ici le html");
        fclose($newView);
      } else {
        $valid = false;
      }
      
      // Création du dossier Public/js et Public/css
      if(!mkdir($dirPathPublic. '/css', 0644, true) || !mkdir($dirPathPublic. '/js', 0644, true)){      
        $valid = false;
      }
      
      
      if($valid){
        $data = ["type"=>"ok", "msg" => "Le bundle <strong>{$nomFormatBundle}</strong> est correctement créé. N'oubliez pas de lui créer une route." ];
      }
      
    } else {
      $data = ["type"=>"ko", "msg" => "Le bundle <strong>{$nomFormatBundle}</strong> existe déjà." ];
    }
    echo json_encode($data);
  }
}