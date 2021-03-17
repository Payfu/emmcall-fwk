<?php
namespace core\Tools;

/**
 * Description of GeoPlugin
 * Utilise le service gratuit de geoplugin
 * @author emmanuel.callec
 */
class GeoPlugin
{
  private $_plugin_adresse;
  private $_data;
  private $_ip;
  private $_geo_city;
  private $_geo_region;
  private $_geo_regionCode;
  private $_geo_regionName;
  private $_geo_countryCode;
  private $_geo_countryName;
  private $_geo_continentCode;
  private $_geo_continentName;
  private $_geo_latitude;
  private $_geo_longitude;
  
  
  public function __construct( $ip = false)
  {
    $this->_ip = $ip ?? $_SERVER['REMOTE_ADDR'];
    $this->_plugin_adresse = "http://www.geoplugin.net/php.gp?ip={$this->_ip}";
    $this->connexion();
    $this->setAllData();
  }
  
  /*
   * On récupère les données à distance
   */
  private function connexion()
  {
    $this->_data = unserialize(file_get_contents($this->_plugin_adresse));
  }
  
  // Initialisation des paramètre
  private function setAllData()
  {
    $this->_geo_city          = $this->_data['geoplugin_city'];
    $this->_geo_region        = $this->_data['geoplugin_region']; 
    $this->_geo_regionCode    = $this->_data['geoplugin_regionCode'];
    $this->_geo_regionName    = $this->_data['geoplugin_regionName'];
    $this->_geo_countryCode   = $this->_data['geoplugin_countryCode'];
    $this->_geo_countryName   = $this->_data['geoplugin_countryName'];
    $this->_geo_continentCode = $this->_data['geoplugin_continentCode'];
    $this->_geo_continentName = $this->_data['geoplugin_continentName'];
    $this->_geo_latitude      = $this->_data['geoplugin_latitude'];
    $this->_geo_longitude     = $this->_data['geoplugin_longitude'];
  }
  
  // Tous les getters
  public function getCity()           : string {  return $this->_geo_city ?? "NULL"; }
  public function getRegion()         : string {  return $this->_geo_region ?? "NULL"; }
  public function getRegionCode()     : string {  return $this->_geo_regionCode ?? "NULL"; }
  public function getRegionName()     : string {  return $this->_geo_regionName ?? "NULL"; }
  public function getCountryCode()    : string {  return $this->_geo_countryCode ?? "NULL"; }
  public function getCountryName()    : string {  return $this->_geo_countryName ?? "NULL"; }
  public function getContinentCode()  : string {  return $this->_geo_continentCode ?? "NULL"; }
  public function getContinentName()  : string {  return $this->_geo_continentName ?? "NULL"; }
  public function getLatitude()       : string {  return $this->_geo_latitude ?? "37.243056"; } // Area 51
  public function getLongitude()      : string {  return $this->_geo_longitude ?? "-115.813056"; } // Area 51
  
  /*
   * Permet de récumérer l'ensebme des données sous la forme d'un tableau
   */
  public function getArrayLocations() : array 
  {
    return 
    [
      "geo_city"          => $this->getCity(), 
      "geo_region"        => $this->getRegion(), 
      "geo_regionCode"    => $this->getRegionCode(),
      "geo_regionName"    => $this->getRegionName(),
      "geo_countryCode"   => $this->getCountryCode(),
      "geo_countryName"   => $this->getCountryName(),
      "geo_continentCode" => $this->getContinentCode(),
      "geo_continentName" => $this->getContinentName(),
      "geo_latitude"      => $this->getLatitude(),
      "geo_longitude"     => $this->getLongitude()
    ];
  }
}
