<?php

/**
 * IMPORTANT : Chaque nom de fichier doit correspondre au nom de la classe qu'il contient.
 */
namespace App;


class Autoloader {
    
    static function register()
    {
        /* 
            * Le premier paramètre du tableau est le nom de la classe courante
            * Le second est le nom de la méthode à appeler.
            */
        spl_autoload_register(array(__CLASS__,'autoload'));
    }
    
    /**
        * 
        * La fonction autoload a été convertie en __autoload() grâce à spl_autoload_register
        * $class_name n'est autre que le nom des classes appelées dans les scripts -> new nomClass();
        */
    static function autoload($class)
    {
      // On autoload uniquement les classes contenues dans le namespace courant
      if(strpos($class, __NAMESPACE__ . '\\') === 0)
      {
        // Pour éviter le problème de namespace, on retire le nom de celui-ci
        $class = str_replace(__NAMESPACE__ . '\\', '', $class);

        // Les sous-namespace sont convertis en chaîne UNIX
        $class = str_replace('\\', '/', $class);
        
        // BUNDLE
        // Si on demande le AppController via un bundle, ex : app/src/TestBundle/Controller/AppController.php
        $exp = explode('/', $class);
        if(end($exp) === 'AppController'){$class = 'src/'.end($exp);}
        
        //Url de la classe à appeler. La constante magique DIR contient le nom du dossier parent.
        require_once __DIR__ .'/'. $class . '.php';
      }
    }
}

Autoloader::register();