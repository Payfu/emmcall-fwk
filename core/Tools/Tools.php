<?php
namespace Core\Tools;

use Core\Password\Password;

/**
 * Fonctions diverses et utiles
 * @author EmmCall
 */
class Tools
{
  /**
   * Suppression des accents
   * @param string $str
   * @param string $charset
   * @return string
   */
  public static function removeAccents($str, $charset='utf-8') : string
  {
    // transformer les caractères accentués en entités HTML
    $str = htmlentities($str, ENT_NOQUOTES, $charset);

    // remplacer les entités HTML pour avoir juste le premier caractères non accentués
    // Exemple : "&ecute;" => "e", "&Ecute;" => "E", "Ã " => "a" ...
    $str = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
    // Remplacer les ligatures tel que : Œ, Æ ...
    // Exemple "Å“" => "oe"
    $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); // pour les ligatures e.g. '&oelig;'

    // Supprimer tout le reste
    $str = preg_replace('#&[^;]+;#', '', $str); // supprime les autres caractères

    return $str;
  }
    
  /*
   * Convertisseur de date
   * $date = 04/05/1979
   * $patternIn = "d/m/Y"
   * $patternOut = "Y-m-d"
   */
  public static function dateToDate(string $date, string $patternIn, string $patternOut ): string{
    $d = \DateTime::createFromFormat($patternIn, $date);
    return $d->format($patternOut);
  }
    
  /*
   * Récupère une date en fonction de sa définition en anglais.
   * $date = 04/05/1979
   * $english = "first day of this month"
   * $patternOut = "Y-m-d"
   */
  public static function dateFromString(string $date, string $english, string $patternOut): string{
    $d = new \DateTime( $date );
    $d->modify( $english );
    return $d->format($patternOut);
  }
    
  /*
   * Y-m-d => jour 00 mois
   * ou ce que l'on veut selon le format
   * http://php.net/manual/fr/function.strftime.php
   * strftime gérant mal les lettres accentuées on y ajoute un utf8_encode
   */
  public static function dateToFr( string $date, $format="%A %d %B" ){
    setlocale (LC_TIME, 'fr_FR.utf8','fra'); 
    return utf8_encode(strftime($format, strtotime($date)));
  }

  /*
   * Vérifie la validité d'une date : 2020-02-30 = false
   * Retourne bool
   */
  public static function validateDate($date, $format = 'Y-m-d H:i:s') : bool{
    $d = \DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
  }
    
  /*
   * Suppression de TOUS les caractères spéciaux (accent inclus), on ne garde que les alphanum 
   * $replaceBy est ce qui remplacera les caractères speciaux (espace vide par défaut) et on supprime les éventuels doublons (ex: --- => -)
   */
  public static function removeSpecChar($str, $replaceBy=false) : string
  {
    $rep = $replaceBy ?? '';
    $str = preg_replace("#[^a-zA-Z0-9]#", $rep, $str);
    if($replaceBy){ $str = preg_replace('#(?:(['.$rep.'])\1)\1*#', $rep, $str); }
    return $str; 
  }
    
  /*
   * Réorganise un tableau en fonction de ses index
   * ex:
   * $array = array(0 => 'a', 1 => 'b', 2 => 'c', 3 => 'd');
   * $newOrderArray = array(3, 2, 0, 1);
   * return : Array([3] => d [2] => c [0] => a [1] => b)
   */
  public static function reorderArray(array $array, array $newOrderArray, array &$c = []) : array{
    //$c = [];
    foreach($newOrderArray as $index) {
        $c[$index] = $array[$index];
    }
    return $c;
  }
    
  /*
   * Slugify
   */
  public static function slugify($str)
  {
    // replace non letter or digits by -
    $text = preg_replace('~[^\pL\d]+~u', '-', $str);

    // transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);

    // trim
    $text = trim($text, '-');

    // remove duplicate -
    $text = preg_replace('~-+~', '-', $text);

    // lowercase
    $text = strtolower($text);

    if (empty($text)) {
      return 'n-a';
    }

    return $text;
  }

  /*
   * GeoIp
   * Retourne les informations de location d'une ip
   * On utilise le web service : https://www.geoplugin.com/webservices/php
   * Pour réaliser la même chose en local il faut la librairie geoip de php https://www.php.net/manual/fr/book.geoip.php ainsi que les bdd de https://www.maxmind.com/en/home
      [geoplugin_request] => xxx.xxx.xxx.xxx
      [geoplugin_status] => 200
      [geoplugin_delay] => 2ms
      [geoplugin_credit] => Some of the returned data includes GeoLite data created by MaxMind, available from <a href=\'http://www.maxmind.com\'>http://www.maxmind.com</a>.
      [geoplugin_city] => Achicourt
      [geoplugin_region] => Hauts-de-France
      [geoplugin_regionCode] => 62
      [geoplugin_regionName] => Pas-de-Calais
      [geoplugin_areaCode] => 
      [geoplugin_dmaCode] => 
      [geoplugin_countryCode] => FR
      [geoplugin_countryName] => France
      [geoplugin_inEU] => 1
      [geoplugin_euVATrate] => 20
      [geoplugin_continentCode] => EU
      [geoplugin_continentName] => Europe
      [geoplugin_latitude] => 50.2743
      [geoplugin_longitude] => 2.7578
      [geoplugin_locationAccuracyRadius] => 50
      [geoplugin_timezone] => Europe/Paris
      [geoplugin_currencyCode] => EUR
      [geoplugin_currencySymbol] => &#8364;
      [geoplugin_currencySymbol_UTF8] => €
      [geoplugin_currencyConverter] => 0.8925
   */
  public static function geoId($ip) : array{
    return unserialize(file_get_contents("http://www.geoplugin.net/php.gp?ip={$ip}"));
  }

  /*
   * On ne garde que les mots qui ont au minimum la taille $length (défaut 4)
   */
  public static function keepWords($str, $length=4, $delimiter=' ') : string
  {
    $strSanitize = '';
    foreach (explode($delimiter, $str) as $value) {
        if(strlen($value) >= $length){ $strSanitize .= $delimiter.$value;}
    }
    return substr($strSanitize, 1);
  }

  /*
      On format une date Ymd -> dmY
  */
  public static function formatDateTime($str, $select=null)
  {
    $datetime = explode(' ', $str);

    list($year, $month, $day) = preg_split('/[-\/.]/', $datetime[0]);

    return $day.'-'.$month.'-'.$year;
  }

  /*
   *  Fonction mail
   * $to : email du contact du site
   * $from : email de la personne qui contact le site
   * $subject : l'objet du message
   */
  public static function sendEmail($emailTo, $from, $subject, $message) : bool
  {
    $rn = "\n"; // Passage à la ligne (normalement on \r\n mais hotmail crée un brug en convertissant le \n en \r\n)

    $headers =  'From: '. $from . $rn .
                'Reply-To: '. $from . $rn .
                'X-Mailer: PHP/' . phpversion();


    return mail($emailTo, $subject, $message, $headers);
  }

  /*
   * On check un dossier et on regarde son contenu
   * Il retourne une valeur de 0 à 2
   * $path = nécessite un chemin ABSOLU => ROOT
   */
  public static function ctrlFolder($path) : int
  {
    $fichierTrouve=0;
    if (is_dir($path) and $dh = opendir($path))
    {
      while (($file = readdir($dh)) !== false && $fichierTrouve==0){ if ($file!="." && $file!=".." ) { $fichierTrouve=1;} }
      closedir($dh);             
    }
    // Le répertoir n'existe pas
    elseif(!is_dir($path))                           {             $val = 0;     }
    // Le répertoire existe mais il est vide
    if(is_dir($path) and $fichierTrouve == 0)        {             $val = 1;     }
    // Le répertoire contient des fichiers
    if(is_dir($path) and $fichierTrouve == 1)        {             $val = 2;     }

    return $val; 
  }


  /*
   * On supprime le contenu d'un dossier, retour true si l'action réussie
   */
  public static function viderDossier($path) : bool
  {
    $var = false;
    if($dh = opendir($path))
    {            
      // On lit chaque fichier du répertoire dans la boucle.
      while (false !== ($file = readdir($dh))) 
      {
        // Si le fichier n'est pas un répertoire…
        // On efface le fichier
        if ($file != ".." AND $file != "." AND !is_dir($file)){ unlink($path.$file); }
      }
      $var = true;
      closedir($dh); 
    }

    return $var;
  }
    
    /*
   * On crypte la valeur en la glissant entre deux chaînes aléatoires de 5 caractères
   * On encode en base64 mais on retire le "=" à la fin
   */
  public static function encodeStr($str = false) : string {  
  
    if(!$str){ exit("Veuillez indiquer une chaîne à encoder"); }
 
    // On encode la chaine base 64
    //$base64 = base64_encode( $this->generate(5).$str.$this->generate(5) );
    $base64 = base64_encode( Password::generate(5).$str.Password::generate(5) );
    // On compte le nom de signe '=' à la fin
    $n = substr_count($base64,"=");
    // S'il y a au moins un =
    if($n > 0){
      $base64 = substr( $base64, 0, '-'.$n);
    }
    // On place le nombre de '=' en fin de chaine
    $str = $base64.$n;
    
    return str_replace("/", "-", strrev($str) );
  }
  
  /*
   * On décode la chaîne pour récupérer la partie qui nous intéresse
   * On remet le '=' à la fin puis on décode
   * Enfin on retire les 5 premiers et 5 derniers caractères de la chaine
   */
  public static function decodeStr($str) : string {
    
    // on retourne la variable sans modification si celle-ci à une taille inférieure à 10 (voir encodeStr( )) 
    if(strlen($str) <= 10){ return $str; }
    
    $a = str_replace("-", "/", $str);
    $s = strrev($a);
    // on récupère le dernier chiffre
    $n = substr($s, -1);
    // on récupère la chaine SANS le dernier chiffre
    $s = substr($s, 0, -1);
    // On répète le '=' autant de fois que nécessaire
    $nn = str_repeat("=", $n);
    // On ajoute les '=', s'ils extiste
    $str = $s.$nn;
    // On décode
    $cDecode = base64_decode($str);
    // On récupère la chaîne
    return substr(substr($cDecode, 0, -5), 5) ;
  }
    
  /**
   * Création d'un id unique
   * 
   * return string (len: 27) : 02da40d8f456ae570628802b731
   */
  public static function generateId() : string
  {
    return bin2hex(random_bytes(7)).uniqid();
  }
}