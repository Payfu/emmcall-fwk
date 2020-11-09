<?php
namespace Core\Table;

#use Core\Yaml\YamlParser;
//use Core\Router\Router;
use Core\Tools\Tools;
use Core\Config;
use Core\Yaml\YamlParseFilePhp;
/**
 * Description of Cache
 * Permet des actions sur le cache
 * @author emmanuel.callec
 */
class Cache
{
  private $_rootPath;
  private $_fileName;
  private $_fullPath;
  private $_timeCache;
  private $_timePurge;
  private $_sizePurge;
  private $_statement;
  private $_attributes;
  private $_one;
  private $_finalQuery;
  
  public function __construct()
  {
    // On charge la classe YamlParseFilePhp
    $yamlPhp = new YamlParseFilePhp();
    // On récupère le fichier converti en tableau
    $array = READ_YAML ? yaml_parse_file(ROOT. '/config/config.yml') : $yamlPhp->convertYamlToArray(ROOT. '/config/config.yml');
    $this->_timePurge = isset($array['cache']) ? $array['cache']['purge'] * 60 : false; // En minute
    $this->_sizePurge = isset($array['cache']) ? $array['cache']['size'] * 1000 : false;// En Kilo octet
    $this->_rootPath = ROOT."/core/Table/tmp";
  }
  /*
   * Mise en cache
   */
  public function addCache($data, $finalQuery){
    // On initialise les paramètres
    $this->initData($data, $finalQuery);
    
    // Si le fichier ($_fileName;) existe quelque part dans l'arbo du dossier core/Table
    $filename = $this->scanArbo($this->_rootPath);
    
    /*
       *  Le fichier existe on reprend son chemin d'origine
       *  Si le fichier est expiré, on le crée à nouveau au même endroit
       */
    if($filename){
      // Si la date est dépassée alors on met à jour le fichier temporaire
      // et on retourne aussi les données
      if (filemtime($filename)<time()-($this->_timeCache)){
        
        // On ouvre le fichier
        $fd = fopen($filename, "w"); // on ouvre le fichier cache
        if ($fd) {
          $contenuCache = serialize( $this->_finalQuery );
          fwrite($fd,$contenuCache); // on écrit le contenu du buffer dans le fichier cache
          fclose($fd);
          // On retourne les data
          return unserialize($contenuCache);
        }
      }else{
        // Les données existe dans le cache on les retourne
        $f = fopen($filename, "rb");
        $v = fread($f, filesize($filename));
        return unserialize($v); // affichage du contenu du fichier
      }
    }
    /*
       *  le fichier n'existe pas on le crée
       */
    else {
      // Si le dossier tmp/{dateHeure}/{file}.dat n'existe pas on le crée
      $this->createDir($this->_fullPath);
      // On ouvre le fichier
      $fd = fopen($this->_fullPath, "w"); // on ouvre le fichier cache
      if ($fd) {
        $contenuCache = serialize( $this->_finalQuery );
        fwrite($fd,$contenuCache); // on écrit le contenu du buffer dans le fichier cache
        fclose($fd);
        
        // On purge les fichier obsolètes
        $this->purge();
        
        // On retourne les data
        return unserialize($contenuCache);
      }
    }
  }
  
  /*
   * On initialise les datas
   */
  private function initData($arr, $finalQuery) {
    $this->_timeCache   = $arr['timeCache'];
    $this->_statement   = $arr['statement'];
    $this->_attributes  = $arr['attributes'];
    $this->_one         = $arr['one'];
    // On transforme le tableau $attributes en string
    $attr = '';
    if($this->_attributes){ $attr = implode(',',$this->_attributes);}
    // On crée le nom du fichier
    $this->_fileName    = sha1($this->_statement.",".$attr.",".$this->_one);
    // La requête finale envoyée par la classe Table
    $this->_finalQuery = $finalQuery;
    // fullPath est le chemin complet fichier inclus
    $this->_fullPath =  ROOT."/core/Table/tmp/".date("Y_m_d/H")."/{$this->_fileName}.dat";
  }
  
  /*
   * Création du dossier
   */
  private function createDir($filename){
    $dirname = dirname($filename);
    if (!is_dir($dirname)){ mkdir($dirname, 0755, true); }
  }
  
  /*
   * On scan l'arboressance du dossier indiqué et on retourne le fichier trouvé
   */
  private function scanArbo($path){
    $di = new \RecursiveDirectoryIterator($path);
    $fichierFinal = false;
    // $file peut être lu avec SplFileInfo, ex: $file->getSize();
    foreach (new \RecursiveIteratorIterator($di) as $filename => $file) {
      // Si c'est un fichier
      if($file->isFile()){
        if(preg_match("/{$this->_fileName}/", $filename)){
          $fichierFinal = $filename;
        }
      }
    }
    return $fichierFinal;
  }
  
  /*
   * ATTENTION IMPORTANT : SplFileInfo::getATime() équivalent à la fonction fileatime() peut-être désactivé sur certain système UNIX auxquels cas utiliser filemtime() ou SplFileInfo::getMTime()
   * On purge le dossier core/Table/tmp
   * Cette méthode est utilisé que lorsqu'un fichier est créé
   */
  public function purge(){
    // On scan l'arboressance
    $di = new \RecursiveDirectoryIterator($this->_rootPath);
    foreach (new \RecursiveIteratorIterator($di) as $filename => $file) {
      // Si c'est un fichier et que son extension est .dat
      if($file->isFile() && $file->getExtension() == 'dat'){
        // Les fichier non consultés depuis au moins x minute et qu'il est inférieur à 200ko
        if ($file->getATime() < time()-($this->_timePurge) && $file->getSize() < $this->_sizePurge ){
          //$this->debug(date("Y-m-d H:i:s",$file->getATime()), false);
          unlink($filename);
        }
      }
    }
  }
  
  private function debug($var, $exit = true){
    echo "<pre>";
    print_r($var);
    echo "</pre>";
    if($exit){ exit; }
  }
}
