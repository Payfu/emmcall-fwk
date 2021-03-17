<?php
namespace Core\Yaml;

/**
 * Cette classe fait le même travail que la fonction yaml_parse_file( )
 * Elle est utile lorsque l'extension YAML n'est pas installée sur le serveur
 */
class YamlParseFilePhp
{  
  /*
   * @param string url de du fichier yaml
   */
  public function convertYamlToArray(string $fileName){
    
    
    $arrFinal = [];
    foreach (new \SplFileObject($fileName) as $line) {
      
      
      // Si le premier caractère de la ligne est un '#' alors c'est un commentaire et on ne tient pas compte de cette ligne.
      // Mais il faut également que la ligne contienne au moins 2 caractères de type alphanumérique
      if(substr(trim($line), 0,1) !== '#' && preg_match("/[a-z0-9_,]{2}/", $line)){
        
        // On compte le nombre de fois où les 4 espaces (obligation YAML) sont présents
        $nv = $this->checkLine($line);
        // On enlève les espaces avant et après
        $cLine = trim($line);        
               
        // Niveau 0 : Nom de route et import@
        if($nv == 0){
          // Première clef de tableau avec retrait du ":"
          $keyNv0 = preg_replace('/:/', '', $cLine);
          $arrFinal[$keyNv0] = [];
        }
        
        // Niveau 1 : path, defaults, requirements et - nomFichierRoute
        if($nv == 1){  
          // Il y a 3 cas possibles
          // 1/ = aucun ':' ce sont les noms de fichier => "- nomFichierRoute"
          // 2/ = Il y a les ':', on explode val[0] = string et val[1] = ""
          // 3/ = Il y a les ':', on explode val[0] = string et val[1] = string
          
          // cas 2 et 3
          if(preg_match("/:/", $cLine)){
            // On explode qu'à la première occurrence
            $v = explode(':', $cLine, 2);
            // cas 2
            if($v[1] === ""){
              $arrFinal[$keyNv0][$v[0]] = [];
            } 
            // cas 3
            else {
              $arrFinal[$keyNv0][$v[0]] = $this->convertBool(trim($v[1]));
            }
          }
          // cas 1
          else {
            $arrFinal[$keyNv0][] = preg_replace(['/-/','/ /'], '', $cLine);
          }
          $keyNv1 = isset($v[0]) ? $v[0] : null;
        }
        
        // Niveau 2 : _controller, _methode ou clef libre
        if($nv == 2){
          $v = explode(':', $cLine, 2);
          if(isset($v[1])){
            // On supprime les espaces avant et après mais aussi les éventuelles '"' (guillement double avant et après)
            $arrFinal[$keyNv0][$keyNv1][$v[0]] = $this->convertBool( trim(trim($v[1]), '"') );
          } else {
            
            $arrFinal[$keyNv0][$keyNv1] = $this->convertBool( trim(trim($v[0]), '"') );
          }
          $keyNv2 = isset($v[0]) ? $v[0] : null;
        }
        
        // Niveau 3 : informations de connexion à la base de donnée
        if($nv == 3){
          $v = explode(':', $cLine, 2);
          // si les données sont présentées comme ceci : "db_type: sqlsrv/mysql" ou juste ceci "sqlsrv/mysql"
          if(isset($v[1])){
            // On supprime les espaces avant et après mais aussi les éventuelles '"' (guillement double avant et après)
            $arrFinal[$keyNv0][$keyNv2][$v[0]] = $this->convertBool( trim(trim($v[1]), '"') );
          } else {
            $arrFinal[$keyNv0][$keyNv2] = $this->convertBool( trim(trim($v[0]), '"') );
          }
        }
      }
    }
    return $arrFinal;    
  }
  
  /*
   * Si la ligne contient une séquence de 4 espaces => '    '
   * Retourne int
   */
  private function checkLine($line):int{
    // On identifie les chaînes qui commencent par un espace.
    // On tiens compte du début de la chaîne car il n'est pas impossible qu'il y ai des espaces en fin de chaîne.
    $matches = [];
    if(preg_match('/(^\s{4,})/i', $line, $matches)){
      // On compte le nombre de fois où les 4 espaces (obligation YAML) sont présents
      return substr_count ($matches[0], '    ');
    }
    return 0;
  }
  
  /*
   * Si les chaînes : TRUE, FALSE et NULL sont présentes alors on les converti dans leurs valeurs respectives
   */
  private function convertBool($str){
    // Si boolean, voir aussi https://www.w3schools.com/php/filter_validate_boolean.asp
    if(in_array(strtolower($str), ['true', 'false'])){  return mb_strtolower($str) === 'true'? true: false; }
    // Si NULL
    if(mb_strtolower($str) === 'null'){ return null; }
    // On ne fait rien.
    return $str;
  }
}
