<?php
namespace Core\Controller;

use Core\Controller\Controller;
//use App;
use Core\Router\Route;

/**
 * Description of RenderController
 * Cette page est importante car elle détermine l'url des views et le nom de la page du template
 * Ces variables son appelées dans Core\Controller\Controller
 * @author EmmCall
 */
class VueController extends Controller
{
  
  /**
   * Initialisation des variables
   * Toutes ces variable sont lu dans core/controller/controller.php
   * @param1 = nom de la classe enfant
   * @param2 = nom du template cible
   */
  // private $currentClass;
  protected $template = 'default';
  
  public function __construct($bundleName, $newTemplate = null)
  {
    if(!is_null($newTemplate)){ $this->template = $newTemplate; }
    
    $this->viewPath     = ROOT    . "/App/Src/{$bundleName}Bundle/Views/";
    $this->templatePath = ROOT    . "/App/Templates/";
    $this->jsPath       = WEBROOT . "/scripts/{$bundleName}/js/";
    $this->cssPath      = WEBROOT . "/scripts/{$bundleName}/css/";
  }
  
  /*
   * On applique de la page.
   * Si $getHtml = false alors il retourne le html de la page afin, par exemple, de l'envoyer par mail. (idée de Guillaume Dauchez)
   * @view = nom de la vue
   * @variables = liste des variables initialisées que l'on retrouve dans la vue
   * @getHtml = booleen
   */
  protected function render(string $view, array $variables = [], bool $getHtml = false){ 
    
    if (!file_exists($this->viewPath . str_replace('.', '/', $view) . '.php')) {
      die("Attention: le fichier <b><i>{$view}</i></b> n'existe pas ! <br /> Vérifiez qu'il n'y a pas d'erreur dans le chemin suivant: <pre>".$this->viewPath . str_replace('.', '/', $view) . '.php'."</pre>");
    }
    
    $content = '';
    ob_start();
    // Les variables récupéré ici sont utilisé en aval dans le template
    extract($variables);
    // On charge le chemin de la page à afficher
    require ($this->viewPath . str_replace('.', '/', $view) . '.php');
    // On affiche la page web sinon on retourne le HTML
    if(!$getHtml){
      // La variable $content est envoyée dans le template
      $content = ob_get_clean();
      // On charge le template
      require($this->templatePath . '/' . $this->template . '.php'); 
      // Nous sommes en dev, une ligne rouge apparaît
      // Par principe on place les paramètres de bootstrap
      if(ENV === 'dev'){
        echo '<div class="col bg-danger" style="background-color:#f00; position: fixed; bottom: 0; right: 0; width: 100%; height: 7px; z-index: 99999;">
              </div>';
      }
    } else {
      $include = ob_get_contents();
      ob_end_clean();
      // On retourne le html
      return $include; 
    }
  }
  
  /*
   * Appel des scripts JS et CSS en fonction des pages
   * Avec la syntaxe suivante : $tab = $this->scripts(['upload.css', 'upload.js', 'https://domaine.fr/script.min.js', 'home::script.js']);
   */
  protected function scriptsManager($tab=[])
  {
    $scripts_js = $scripts_css = $scripts_json = '';
    if(!empty($tab))
    {
      foreach ($tab as $script) {
        // Pour chaque script on récupère son url(relative ou absolut), type, extention et bundle
        list($script, $type, $ext, $nomBundle) = $this->scriptParser($script);
        
        // Si ce n'est pas une url
        if($type === 'isUrl'){
          $url = $script;
        }else {
          // Si un bundle est indiqué on le remplace dans le path
          $url = !is_null($nomBundle) ? str_replace(Route::$_current_bundle.'/'.$ext, $nomBundle.'/'.$ext, $this->{$ext.'Path'}.$script) : $this->{$ext.'Path'}.$script ;
        }
        
        if  ($ext === 'css') { $scripts_css .= "\t".'<link rel="stylesheet" href="'.$url.'">'."\n"; }
        elseif($ext === 'js') { $scripts_js .= "\t".'<script src="'.$url.'"></script>'."\n"; }        
      }
    }
    return compact('scripts_js', 'scripts_css');
  }
  
  
  /*
   * On parse la valeur afin de savoir si c'est une url, un fichier ou un fichier depuis un bundle
   */ 
  private function scriptParser(string $script) : array{
    $nomBundle = null;
    // On supprime les éventuels espaces
    $script = str_replace(' ', '', $script);
    // Si c'est un cdn
    if(str_starts_with($script, 'https://')) { $type= 'isUrl'; } 
    // Sinon si c'est dans un autre bundle
    else if(strstr($script, '::')) { $type= 'isNotUrl'; $nomBundle = ucfirst(strtolower(explode('::', $script)[0])); $script = explode('::', $script)[1]; }
    // Sinon c'est un fichier simple
    else { $type= 'isNotUrl'; }
    
    if(str_ends_with(strtolower($script), '.js') ){ $ext = 'js'; }
    else if(str_ends_with(strtolower($script), '.css') ){ $ext = 'css'; }
    //else if(str_ends_with(strtolower($script), '.json') ){ $ext = 'json'; }
    else { die("Attention: veuillez vérifier l'extention de {$script}"); }
    return [$script, $type, $ext, $nomBundle];
  }
}
