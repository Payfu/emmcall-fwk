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
        $nv = substr_count ($line, '    ');
        
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
              $arrFinal[$keyNv0][$v[0]] = trim($v[1]);
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
          // On supprime les espaces avant et après mais aussi les éventuelles '"' (guillement double avant et après)
          $arrFinal[$keyNv0][$keyNv1][$v[0]] = trim(trim($v[1]), '"');
        }
      }
    }
    
    return $arrFinal;    
  }
}