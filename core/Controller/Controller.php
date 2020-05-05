<?php
namespace Core\Controller;
use Core\Router\Routing;

/**
 * Description of Controller
 *
 * @author EmmCall
 */
class Controller
{
    /*
     * C'est variable son créer dans App\AppController
     */
    protected $viewPath;
    protected $template;
    protected $templatePath;
    protected $jsPath;
    protected $cssPath;
    protected $_ymlFile = ROOT."/app/Routes/routes.yml";



    protected function render($view, $variables = [])
    {      
      $content = '';
      //var_dump($this->viewPath. str_replace('.', '/', $view));
      ob_start();

      // Les variables récupéré ici sont utilisé en aval dans le template
      extract($variables);

      // On charge le chemin de la page à afficher
      require ($this->viewPath . str_replace('.', '/', $view) . '.php');

      // La variable $content est envoyée dans le template
      $content = ob_get_clean();

      // On charge le template
      require($this->templatePath . 'templates/' . $this->template . '.php'); 
    }
    
    /*
     * On tape le nom d'une route et il récupère le chemin "path"
     * @routeName = nom de la route
     * @$params = tableau des paramètres GET de la route
     */
    protected function redirect($routeName, $params = null, bool $redirect=true){
      $r  = new Routing($this->_ymlFile, null);
      return $r->redirectManager($routeName, $params, $redirect);
    }
    
    /*
     * Appel des scripts JS et CSS en fonction des pages
     * Avec la syntaxe suivante : $tab = $this->scripts(['upload.css', 'upload.js', 'https://domaine.fr/script.min.js']);
     */
    protected function scripts($tab=[])
    {
        $scripts_js = $scripts_css = '';
        if(!empty($tab))
        {   
            foreach ($tab as $value) {
                $sanitize_name = str_replace(' ', '', $value); // On supprime les éventuelles espaces
                $ext = strtolower(substr(strrchr($sanitize_name, '.'), 1)); // On récupère l'extension sans le point

                if(filter_var($sanitize_name, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED))
                { $url = filter_var($sanitize_name, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED); $type= 'isUrl'; }
                else{$url = $sanitize_name; $type = 'isNotUrl';}

                if    ($ext === 'css' and $type === 'isUrl') { $scripts_css .= "\t".'<link rel="stylesheet" href="'.$url.'">'."\n"; }
                elseif($ext === 'css' and $type === 'isNotUrl') { $scripts_css .= "\t".'<link rel="stylesheet" href="'.$this->cssPath.$url.'?'.uniqid().'">'."\n"; }
                elseif( $ext === 'js' and $type === 'isUrl') { $scripts_js .= "\t".'<script src="'.$url.'"></script>'."\n"; }
                elseif( $ext === 'js' and $type === 'isNotUrl') { $scripts_js .= "\t".'<script src="'.$this->jsPath.$url.'?'.uniqid().'"></script>'."\n"; }
            }
        }
        return compact('scripts_js', 'scripts_css');
    }

    /**
     * Renvoie les bon header en fonction de la situation
     */
    protected function forbidden(string $value=null)
    {
      // On enregistre $value en session
      if($value <> null){
        $_SESSION['SESS_ERROR_403'] = $value;
      }
      $url = WEBROOT.'/error403';
      // On redirige vers la route par defaut du controller app/controller/ErrorController.php
      header('Location:'.$url);
      exit;
    }
    
    /*
     * Gestion de l'erreur 404
     */
    protected function notFound(string $value=null)
    {
      // On enregistre $value en session
      if($value <> null){
        $_SESSION['SESS_ERROR_404'] = $value;
      }
      $url = WEBROOT.'/error404';
      // On redirige vers la route par defaut du controller app/controller/ErrorController.php
      header('Location:'.$url);
      exit;
    }
    
  /*
   * Cette méthode est appelée depuis un nomTableObj.
   * On récupère la liste des champs pour le select d'une entité
   * paramètre à false si on veut un tableau
   * IMPORTANT l'objet doit d'abors passer par get_object_vars( ) avant d'être transmis !
  */
  protected function getFields($entity, $isString = false){
    
    $arrayProprietes = array_keys($entity);
    
    $arr=[];
    foreach ($arrayProprietes as $k) {
      if(substr($k, 0, 2) == '__'){
        // La clef du tableau est le nom du champ (et de la propriété) et la valeur est la propriété
        $arr[] = substr($k, 2);
      }
    }
    // Si isTring = true => array, sinon => string
    return $isString ? $arr : implode(',',$arr);
  }
  
  /*
   * Cette méthode est appelée depuis un nomTableObj.
   * En fonction de si c'est un getEntity, createEntity, ou updateEntity on construit pas de la même façons
   */
  protected function constructEntity($entity, $action, $fields=false, $exclusions = ['id']){
    
    $arrCreateEntity = $arrUpdateEntity=[];
    
    foreach($fields as $k => $v){
      $prop = "__".$k;
      
      // GetEntity
      if($action === 'getEntity'){
        $entity->$prop = $fields->$k;
      }
      
      // createEntity
      if($action === 'createEntity'){
        $prop2 = "__".$v;
          
        // Les exclusions contiennent les champs auto_incrémentés, par défaut: id
        if(!in_array($v, $exclusions) ){
          $arrCreateEntity[$v] = $entity->$prop2;
        }
        
      }
      // updateEntity
      // On récupère les données qui ont été modifié par setNomChamp() dans l'entité
      if($action === 'updateEntity'){
        $arrUpdateEntity[$k] = $entity->$prop;
      }
    }
    
    if($action === 'getEntity'){
      return $entity;
    }
    if($action === 'createEntity'){
      return $arrCreateEntity;
    }
    if($action === 'updateEntity'){
      return $arrUpdateEntity;
    }
  }
}
