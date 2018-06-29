<?php

namespace Core;

/**
 * Par défaut cette classe ira chercher la configuration du site
 * Ceci est un Singleton, c'est à dire : une instance appelée qu'une seule fois.
 *
 * @author EmmCall
 */
class Config
{
    private $settings = [];
    
    /**
     * la variable static $_instance permettra de stocker l'instance dans le Singleton
     * L'underscore sert à différencier les variables static des variables classiques
     */
    private static $_instance; 

    /**
     * Cette methode permet de n'appeler qu'une seule fois l'instance
     */
    public static function getInstance($file)
    {
        
        if(is_null(self::$_instance))
        {
            self::$_instance = new Config($file);
        }
        
        return self::$_instance;
    }

    /**
     * @param type $file
     * $file sera le fichier config que je souhaite charger
     */
    public function __construct($file)
    {
        $this->settings = require($file);
    }
    
    /*
     * Cette methode retourne la clef appelée ex : db_name
     */
    public function get($key)
    {
        if(!isset($this->settings[$key]))
        {
            return null;
        }
        return $this->settings[$key];
    }
}
