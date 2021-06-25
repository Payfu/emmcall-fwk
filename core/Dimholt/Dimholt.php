<?php
namespace core\Dimholt;

use core\DataBase\Config;
use core\Tools\Strings;
use core\Yaml\YamlParseFilePhp;

class Dimholt
{
  private $_dmlt_key;
  private $_dmlt_key_admin;
  private $_dmlt_url;
  public function __construct(){
    // On charge la classe YamlParseFilePhp
    $yamlPhp = new YamlParseFilePhp();
    
    $urlYaml = ROOT . '/config/config.yml';
    $yamlParse = READ_YAML ? yaml_parse_file($urlYaml) : $yamlPhp->convertYamlToArray($urlYaml);
    $config = Config::getInstance($yamlParse);
    
    $this->_dmlt_key = $config->get('dmlt_key');
    $this->_dmlt_key_admin  = $config->get('dmlt_key_admin');
    $this->_dmlt_url = $config->get('dmlt_url');
  }
  
  /*
   * Activation de la clef admin en remplaçant _dmlt_key par $_dmlt_key_admin
   */
  public function adminKey(){
    $this->_dmlt_key = $this->_dmlt_key_admin;
  }
  
  /*
   * On check si l'identifiant et/ou l'ip sont bloqué
   */
  public function check($idUser, bool $json = false){ 
    return $this->sendByPost($idUser, false, $json);
  }
  
  /*
   * En cas d'échec de connexion, on ajoute l'id ou l'ip.
   */
  public function add($idUser, bool $json = false){
    return $this->sendByPost($idUser, 'add', $json);
  }
  
  /*
   * La connexion est un succès on l'enregistre dans le journal
   */
  public function success($idUser, bool $json = false){
    return $this->sendByPost($idUser, 'success', $json);
  }
  
  /*
   * On envoie les données via POST à l'url données
   * @action : add = on ajoute
   */
  private function sendByPost($idUser, $action = false, $json = false){
    
    $postdata = http_build_query(
      array(
        'clef'        => $this->_dmlt_key,
        'id'          => $idUser,
        'ip'          => $this->getIpAddress(),
        'user_agent'  => Strings::encodeStr($_SERVER['HTTP_USER_AGENT']),
        'action'      => $action // Si add alors on considère que l'authentification est erronée, si false alors c'est un contrôle des datas
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
        
    // Si true alors on retourne un json sinon c'est un objet
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
