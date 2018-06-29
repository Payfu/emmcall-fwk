<?php
namespace Core\HTML;
/**
 * Description of Form
 * Permet de générer un formulaire
 * @author EmmCall
 */
class Form 
{
    /**
     * @var array : données $_POST utilisées par le formulaire à l'instantiation de l'objet : new BootstrapForm($_POST);
     */
    private $data;
    
    /**
     *
     * @var string : tag utilisé par le formulaire 
     */
    public $surround = 'p';
    
    /**
     * 
     * @param array $data : données utilisées par le formulaire
     */
    public function __construct($data = array())
    {
        $this->data = $data;
    }
    
    /**
     * 
     * @param type $html string : code html à entourer
     * @return string
     */
    protected function surround($html)
    {
        return "<{$this->surround}> {$html} </{$this->surround}>";
    }

    /**
     * 
     * @param type $index string : index de la valeur à récupérer
     * @return string
     */
    protected function getValue($index)
    {
        //var_dump($this->data);
        if (is_object($this->data)) {
            return $this->data->$index;
        }
        
        return isset($this->data[$index]) ? $this->data[$index] : NULL;
    }
    
    /*
     * On retourne une liste d'options pour un select en fonction de jour, mois, année
     * 
     */
    protected function selectOptions($str, $getValue = null)
    {
        $tabMonth = array("", "janvier", "février", "mars", "avril", "mai", "juin", "juillet", "août", "septembre", "octobre", "novembre", "décembre");
        
        
        
        if($str === 'day')
        {
            //$selected = isset($getValue) 
            $input = '<option value="" disabled selected>Jour</option>';
            for($i = 1; $i <= 30; $i++){  
                
                $selected = ($getValue == $i) ? 'selected' : null ;
                $input .= "<option value='$i' $selected>$i</option>";  
                
            }
        }
        
        if($str === 'month') 
        {
            $input = '<option value="" disabled selected>Mois</option>';
            for($i = 1; $i <= 12; $i++){  
                
                $selected = ($getValue == $i) ? 'selected' : null ;
                $input .= "<option value='$i' $selected>$tabMonth[$i]</option>";  
                
            }
        }
        
        if($str === 'year') 
        {
            $input = '<option value="" disabled selected>Années</option>';
            $currentYear = date("Y");
            $oldYear = date("Y") - 100;
            for($i = $currentYear; $i >= $oldYear; $i--){  
                
                $selected = ($getValue == $i) ? 'selected' : null ;
                $input .= "<option value='$i' $selected>$i</option>";  
                
            }
        }
        
        return $input;
    }
    
    /*
     * @param type $name string
     * @param type $tab array
     * @param type $format string
     * @param type $type string
     */
    protected function radioValues($name, $tabValues=[], $format=null, $type=null, $isChecked=null)
    {
        // Le format checkbox et radio sont identique
        $type = ($type == 'checkbox') ? 'checkbox' : 'radio';
        
        $listRadio = '';
        
        foreach ($tabValues as $k => $v) {
            $attributes = '';
            
            // Si c'est une checkbox name prend la clef
            $chkName = ($type == 'checkbox') ? '_'.$k : null;
            
            // Si la mention :checked s'y trouve
            if ($k == $this->getValue($name.$chkName) or $k == $isChecked) { $attributes = ' checked'; }
            
            if ($format == 'inline') {
                
                $listRadio .= '<label class="'.$type.'-inline" for="'.$name.'_'. $k .'">'
                                .'<input type="'.$type.'" name="'. $name . $chkName .'" id="'.$name.'_'. $k .'" value="'. $k .'" '. $attributes .'>'
                                .$v
                            .'</label>';
            }
            else
            {
                $listRadio .= '<div class="'.$type.'">'
                                .'<label for="'.$name.'_'. $k .'">'
                                  .'<input type="'.$type.'" name="'. $name . $chkName .'" id="'.$name.'_'. $k .'" value="'. $k .'" '. $attributes .'>'
                                  .$v
                                .'</label>'
                              .'</div>';
            }
        }
        
        return $listRadio;
        
        
    }
    
    /**
     * Ici se trouve les diverses infos pour les syntaxes
     */
    public function inputInfo()
    {
        $html = '<legend>Input</legend>';
        $html .= "<code>input</code>( '<code>nameField</code>',
                    '<code>type</code>'=>'text/textarea',
                    ['<code>label</code>'=>'nameLabel',
                    '<code>labelClass</code>'=>'col-md-3 control-label', 
                    '<code>placeholder</code>'=>'string',
                    '<code>divClass</code>' => 'col-md-6'] );";
        return $html."<br /><br />";
    }




    public function radioInfo()
    {
        $html = '<legend>Radio</legend>';
        $html .= "<code>radio</code>( '<code>nameField</code>', 
                    ['<code>label</code>'=>'nameLabel', 
                    '<code>labelClass</code>'=>'col-md-3 control-label', 
                    '<code>divClass</code>' => 'col-md-6',
                    '<code>inline</code>' => 'yes', 
                    '<code>checked</code>'=>'optionKey'] , 
                    
                    [<code>options</code>], <code>true</code> (key = val) );";
        return $html."<br /><br />";
    }
    
    public function checkboxInfo()
    {
        $html = '<legend>Checkbox</legend>';
        $html .= "<code>checkbox</code>( '<code>nameField</code>', 
                    ['<code>label</code>'=>'nameLabel', 
                    '<code>labelClass</code>'=>'col-md-3 control-label', 
                    '<code>divClass</code>' => 'col-md-6',
                    '<code>inline</code>' => 'yes', 
                    '<code>checked</code>'=>'optionKey'] , 
                    
                    [<code>options</code>], <code>true</code> (key = val) );";
        
        $html .= "<br /><br /><pre>Chaque box prend comme nom <strong>nameField_keyOptions</strong></pre>";
        return $html."<br /><br />";
    }
    
    public function selectInfo()
    {
        $html = '<legend>Select</legend>';
        $html .= "<code>select</code>( '<code>nameField</code>',
                    ['<code>label</code>'=>'nameLabel', 
                    '<code>labelClass</code>'=>'col-md-3 control-label', 
                    '<code>divClass</code>' => 'col-md-6',
                    '<code>inline</code>' => 'yes', 
                    '<code>checked</code>'=>'optionKey'] , 
        
                    [<code>options</code>], <code>true</code> (key = val) );";
        
        return $html."<br /><br />";
    }
    

}
