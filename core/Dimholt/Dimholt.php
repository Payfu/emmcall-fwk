<?php
namespace Core\Dimholt;

use Core\Config;

class Dimholt
{
  private $_dmlt_key;
  private $_dmlt_url;
  public function __construct()
  {
    $config = Config::getInstance(yaml_parse_file(ROOT . '/config/config.yml'));
    
    $this->_dmlt_key = $config->get('dmlt_key');
    $this->_dmlt_url = $config->get('dmlt_url');
  }
  
  /*
   * On check si l'identifiant et/ou l'ip sont bloqué
   */
  public function check($idUser, $json = false){ 
    return $this->sendByPost($idUser, false, $json);
  }
  
  /*
   * En cas d'échec de connexion, on ajoute l'id ou l'ip.
   */
  public function add($idUser, $json = false){
    return $this->sendByPost($idUser, true, $json);
  }
  
  /*
   * On envoie les données via POST à l'url données
   * @action : add = on ajoute
   */
  private function sendByPost($idUser, $action = false, $json = false){
    $p = new \Core\Password\Password();
    // qy2q1Cgw655cb86d8a93cec
    $add = $action ? 'add' : false;
    
    $postdata = http_build_query(
      array(
          'clef'        => $this->_dmlt_key,
          'id'          => $idUser,
          'ip'          => $this->getIpAddress(),
          'user_agent'  => $p->encodeStr($_SERVER['HTTP_USER_AGENT']),
          'action'      => $add // Si add alors on considère que l'authentification est erronée, si false alors c'est un contrôle des datas
      )
    );

    $opts = array('http' =>
      array(
        'method'  => 'POST',
        'header'  => 'Content-type: application/x-www-form-urlencoded',
        'content' => $postdata
      )
    );

    $context = stream_context_create($opts);

    $result = file_get_contents($this->_dmlt_url, false, $context);
    
    // Débug
    /*print_r($result);
    exit(); */
   
    // Si true alors on retourne un json sinon c'est un objet
    /*if($json){
      return $result;
    } else {
      return json_decode($result);
    }*/
    
    return $json ? $result : json_decode($result);
  }
  
  /**
    * On récupère l'adresse IP du user et s'il passe par un proxy alors on essaie de passer outre.
    * @return string
    */
  private function getIpAddress() {
    if ( isset( $_SERVER['HTTP_X_REAL_IP'] ) ) {  
      return $_SERVER['HTTP_X_REAL_IP'];
    } elseif ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
      return (string) self::is_ip_address( trim( current( explode( ',', $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) ) );
    } elseif ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
      return $_SERVER['REMOTE_ADDR'];
    }
    return '';
  }
}
