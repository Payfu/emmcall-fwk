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
    protected function forbidden()
    {
        header('HTTP/1.0 403 Forbidden');
        die('Acces refusé');
    }
    
    protected function notFound()
    {
        header('HTTP/1.0 404 Not Found');
        die('Page Introuvable');
    }
}
