<?php

namespace Core\Entity;
/**
 * Description of Entity
 *
 * @author EmmCall
 */
class Entity
{
    /**
     * 
     * @param type $key
     * Cette fonction est une méthode magique qui permet de récupérer une clef ex : url ira chercher getUrl()
     * return get(Key)
     */
    public function __get($key)
    {
        $methode = 'get' . ucfirst($key);
        
        // Pour éviter d'appeler plusieur fois la méthode
        $this->$key = $this->$methode();
        
        return $this->$key;
    }
}
