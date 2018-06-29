<?php

namespace Core\Auth;

use Core\DataBase\DataBase;
use App\Controller\UserController;
/**
 * Description of DBAuth
 *
 * @author emmcall
 */
class DBAuth
{
    private $db;
    
    /**
     * Injection de dépendance pour la connexion à la base de données
     */
    public function __construct()
    {
        //$this->db = $db;
    }
    
    /**
     * Connaitre l'id de l'utilisateur
     */
    public function getUserId()
    {
        if ($this->logged()) {
            return $_SESSION['auth'];
        }
        
        return false;
    }
    
    /**
     * Permettre à un utilisateur avec un username et un password de se connecter
     * $table = table des identifiants
     * $table2 = table utilisateur
     * return boolean (true / false)
     */
    public function login($usermail, $password, $table, $table2, $setcookie = false)
    {
        $user = $table->find(['email'=>$usermail]);
        
        // Si j'ai un utilisateur, je vérifie que son mot de passe est égale à ce qui vient d'être saisi et je sauvegarde le cookie
        if ($user) {
            
            if(password_verify($password, $user->password)) {
                // je hash l'ip pour le rendre incoprehensible en cas de hack.
                $_SESSION['auth'] = $user->ip;
                
                $ipHash = password_hash($user->ip, PASSWORD_DEFAULT);
                
                // Si l'utilisateur souhaite rester connecté
                if($setcookie)
                {
                    // On crée le cookie
                    self::getCookie($ipHash);
                    $table2->update(["ip" => $user->ip], ['session'=>$ipHash, 'connexion'=>date('Y-m-d H:i:s')]);
                }
                else
                {
                    // On ne crée pas le cookie mais on enregistre quand même l'heure d'inscription
                    $table2->update(["ip" => $user->ip], ['session'=>'', 'connexion'=>date('Y-m-d H:i:s')]);
                }
                
                return true;
                
            }
        } 
        return FALSE;
    }
    
    /**
     * On contrôle si l'utilisateur n'est pas déjà connecté
     * @return boolean
     */
    public function logged($table)
    {
	$cookie_name = "johnDoe";
        // Si auth est initialisé
        if(isset($_SESSION['auth']))
        {
            return $_SESSION['auth'];
        }
        
        // Sinon si un cookie 'mesrecettesperso' existe alors on réactive la session auth en allant chercher l'ip dans la table users_session
        elseif (isset($_COOKIE[$cookie_name])) 
        {
            $user = $table->find(['session'=>$_COOKIE[$cookie_name]]);
            
            //var_dump('ici');
            if($user)
            {
                // On met à jour l'heure de connexion
                $table->update(["ip" => $user->ip], ['connexion'=>date('Y-m-d H:i:s')]);
                return $_SESSION['auth'] = $user->ip;
            }
        }
        // Sinon on retourne une valeur null
        else
        {
            return null;
        }
        
    }
    
    /**
     * Déconnexion
     * Destruction du cookie et de la session
     */
    public function logout()
    {   
	    $cookie_name = "johnDoe";
	    if (isset($_COOKIE[$cookie_name])) { 
		    unset($_COOKIE[$cookie_name]);
		    setcookie($cookie_name, null, -1, '/');
            
	    }
	    
	    $_SESSION = array();
	    session_destroy();    
    }
    
    
    /**
     * On crée le cookie
     */
    public function getCookie($ipHash)
    {
        $cookie_name = "johnDoe";
        $cookie_value = $ipHash;
        setcookie($cookie_name, $cookie_value, time() + (86400 * 365), "/"); // 86400 = 1 day
    }
    
}
