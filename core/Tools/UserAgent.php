<?php
namespace Core\Tools;

/**
 * Description of UserAgent
 * Retourne un tableau avec les données concernant l'environement de l'utilisateur
 * @author emmanuel.callec
 */
class UserAgent
{
  
  /*
    [user_agent] => Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.142 Safari/537.36
    [browser_name_regex] => ~^mozilla/5\.0 \(.*windows nt 10\.0.*\).*applewebkit.*\(.*khtml.*like.*gecko.*\).*chrome/.* safari/.*$~
    [browser_name_pattern] => Mozilla/5.0 (*Windows NT 10.0*)*applewebkit*(*khtml*like*gecko*)*Chrome/* Safari/*
    [parent] => Chrome Generic
    [platform] => Win10
    [comment] => Chrome Generic
    [browser] => Chrome
    [browser_maker] => Google Inc
    [device_type] => Desktop
    [device_pointing_method] => mouse
    [version] => 0.0
    [majorver] => 0
    [minorver] => 0
    [ismobiledevice] => 
    [istablet] => 
    [crawler] => 
     */
  
  public $_user_agent;
  
  public function __construct($userAgent)
  {
    $this->_user_agent = $userAgent;
  }
  
  public function getUserAgentInfo() : array{
    $getBrowser = get_browser($this->_user_agent, true);
    
    return array_merge(["user_agent"=>$this->_user_agent],$getBrowser, $this->getBrowserVersion($getBrowser));
  }
  
  /*
   * On récupère la version du navigateur
   */
  private function getBrowserVersion($getBrowser) : array{
    // version du navigateur
    $browser = $getBrowser['browser'];
    
    // Si le nom du navigateur se trouve bien dans le user agent
    if(strstr($this->_user_agent, $browser)){
      $str = explode($browser, $this->_user_agent);

      // S'il y a une espace
      $isSpace = strstr($str[1], ' ', true); // /75.0.3770.142 Safari/537.36

      // Si isSpace = true alors il y a une espace dans : "75.0.3770.142 Safari/537.36" sinon les format est plutôt : "68.0"
      $browserVersion = $isSpace ? substr($isSpace, 1) : substr($str[1], 1);

      return ['browser_version'=>$browserVersion];
    } else {
      return ['browser_version'=>'unknown'];
    }
  }
  
}
