<?php
namespace Core\ExportCSV;


use Core\Tools\Tools;
/**
 * Permet un export d'un tableau au format CSV
 */
class ExportCSV
{
  private $_nom_fichier;
  private $_colonnes;
  private $_data;
  private $_separateur;
  private $_enclosure;
  private $_escape;
  
  public function __construct(string $nomFichier, array $colonnes, array $data, string $separateur = ';', string $enclosure = '"', string $escape = '\\')
  {
    $this->_nom_fichier = $this->ctrlNomFichier($nomFichier);
    $this->_colonnes = $this->ctrlColonnes($colonnes);
    $this->_data = $this->ctrlData($data);
    $this->_separateur = $this->ctrlSeparateur($separateur);
    $this->_enclosure = $this->ctrlEnclosure($enclosure);
    $this->_escape = $this->ctrlEscape($escape);
  }
  
  /*
   * On génère l'export du csv
   */
  public function export(){
    // Entêtes de sortie pour que le fichier soit téléchargé plutôt que affiché
    header('Content-type: text/csv');
    header('Content-Disposition: attachment; filename="'.$this->_nom_fichier.'.csv"');

    // Pas de fichier cache
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // On crer un pointeur de fichier connecté au flux de sortie
    $fichier = fopen('php://output', 'w');
    
    // On converti les caractère en utf8
    fprintf($fichier, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // On envoie les nom de colonnes
    fputcsv($fichier, $this->_colonnes, $this->_separateur, $this->_enclosure, $this->_escape);
    
    // On ajoute les données
    foreach ($this->_data as $ligne)
    { 
      fputcsv($fichier, $ligne, $this->_separateur, $this->_enclosure, $this->_escape);
    }
  }
  
  /*
   * On remplace les espaces par un underscore.
   */
  private function ctrlNomFichier(string $nomFichier){
    if($nomFichier == ''){ die('Veuillez indiquer un nom de fichier.'); }
    if(!is_string($nomFichier)){ die('Veuillez indiquer un nom de fichier.'); } 
    else { return str_replace(' ', '_', $nomFichier); }
  }
  
  /*
   * On vérifie que $_colonnes est bien un array et contient au moins une valeur.
   * ['champ 1', 'champ 2', 'champ 3', ...]
   */
  private function ctrlColonnes(array $colonnes){
    if(count($colonnes) == 0){ die('Veuillez indiquer les noms des colonnes.'); }
    return $colonnes;
  }
  
  /*
   * Data doit être un tableau multidimensionnel
   * [
   *  ['value 1', 'value 2', 'value 3', ...]
   *  ['value 1', 'value 2', 'value 3', ...]
   * ]
   */
  private function ctrlData(array $data){
    if(!is_array($data[0])){ die('Le tableau "data" est vide.'); }
    return $data;
  }
  
  /*
   * On vérifie que le séparateur est bien une chaîne de 1 caratère.
   */
  private function ctrlSeparateur(string $separateur){
    if(strlen($separateur) <> 1){ die('Le séparateur doit-être un caractère unique.'); }
    return $separateur;
  }
  
  /*
   * L'enclosure : caractère utilisé pour entourer le champ (un seul caractère). 
   */
  private function ctrlEnclosure($enclosure){
    if (!is_null($enclosure)){
      if(strlen($enclosure) <> 1){ die('L\'enclosure doit-être un caractère unique.'); }
    } 
    return $enclosure;    
  }
  
  /*
   * Escape : spécifie le caractère d'échappement. La valeur par défaut est "\\". Peut également être une chaîne vide ("") qui désactive le mécanisme d'échappement
   */
  private function ctrlEscape($escape){
    return $escape; 
  }
}
