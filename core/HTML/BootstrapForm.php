<?php
/*
   Dernière modification
   20-05-2016 : ajout de "time" dans la méthode "input"
   20-04-2016 : ajout de private $classFormGroup
   19-04-2016 : select() , ajout de l'attribut "id"
   11-04-2016 : select() , ajout de l'attribut true pour la fonction keyEqualvalue();

 */


namespace Core\HTML;

/**
 * Description of BootstrapForm
 *
 * @author payfu
 */
class BootstrapForm extends Form
{
    
    private $classDefault = 'form-control';
    private $classFormGroup = 'form-group row';
    private $rowsDefault = '4';
    
    /**
     * 
     * @param type $html string code html à entourer
     * @return string
     */
    protected function surround($html)
    {
        return "<div class=\"{$this->classFormGroup}\">{$html}</div>";
    }
    
   /**
     * 
     * @param string $name
     * @param string $label
     * @param array $options
     * @return string
     */
    public function input($name, $options = [])
    {
        
        $labelClass     = isset($options['labelClass']) ? ' class="' .$options['labelClass']. '" ' : '';
        $label          = isset($options['label'])      ? '<label'.$labelClass.' for="' . $name .'">' .  $options['label'] . '</label>' : '';
        $class          = $options['class'] ?? $this->classDefault;
        $rows            = $options['rows'] ?? $this->rowsDefault;
        $type           = isset($options['type'])       ? $options['type'] : 'text';
        $placeholder    = isset($options['placeholder']) ? 'placeholder="' .$options['placeholder'] .'"' : '';
        $value          = isset($options['value']) ? $options['value'] : $this->getValue($name);
        
        
        if ($type === 'textarea') {
            $input = '<textarea name="' . $name .'" id="'. $name .'" class="'. $class. '" '. $placeholder .' rows="'.$rows.'">' . $value . '</textarea>';
        }
        elseif ($type === 'time') {
            $input = '<input type="'. $type .'" name="'. $name .'" id="'. $name .'" value = "' . $value . '" class="'. $class. '" '. $placeholder .'>';
        }
        else{
            $input = '<input type="'. $type .'" name="'. $name .'" id="'. $name .'" value = "' . $value . '" class="'. $class. '" '. $placeholder .'>';
        }
        
        $div = isset($options['divClass']) ? '<div class="'. $options['divClass'] .'">' . $input . '</div>' : $input;
        
        
        return $this->surround($label . $div);
    }


    
    /*
     * Select
     */
    public function select($name, $html, $options, $keyval = false)
    {

        $labelClass     = isset($html['labelClass']) ? ' class="' .$html['labelClass']. '" ' : '';
        $label          = isset($html['label'])      ? '<label'.$labelClass.' for="' . $name .'">' .  $html['label'] . '</label>' : '';
        $class          = isset($html['class'])      ? $html['class'] : $this->classDefault;
        $id             = isset($html['id'])         ? 'id="'.$html['id'].'"' : null;
        $type           = isset($html['type'])       ? $html['type'] : 'text';
        $formSize       = isset($html['size'])       ? 'form-control-'.$html['size'] : null;
        $optionOff      = isset($html['off'])        ? $html['off'] : null;

        // Si true alors la clef est égale à la valeur
        if($keyval == true){ $options = $this->keyEqualvalue($options); }
        
        $input = '<select class="form-control '.$formSize.'" name="'. $name .'" '.$id.'>';
        
        $input .= (!is_null($optionOff)) ? '<option value="" disabled selected>'.$optionOff.'</option>' : null;

        foreach ($options as $k => $v) {
            
            $attributes = '';
            
            if ($k == $this->getValue($name)) { $attributes = ' selected';   }
            
            $input .= "<option value='$k'$attributes>$v</option>";
        }
        
        $input .= '</select>';


        $div = isset($html['divClass']) ? '<div class="'. $html['divClass'] .'">' . $input . '</div>' : $input;
        
        return $this->surround($label . $div);
    }
    
    /*
     * Radio multiple avec ou sans inline
     */
    public function radio($name, $options = [], $values =[], $type=null)
    {
        $labelClass     = isset($options['labelClass']) ? ' class="' .$options['labelClass']. '" ' : null;
        $label          = isset($options['label'])      ? '<label'.$labelClass.' for="'. $name .'">' .  $options['label'] . '</label>' : null;
        $divClass       = isset($options['divClass']) ? ' class="'. $options['divClass'] .'"' : null;
        $format         = (isset($options['inline']) && ($options['inline'] == 'yes')) ? 'inline' : null;
        $isChecked      = isset($options['checked']) ? $options['checked'] : null ;
        
        
        
        
        $input = '<div class="'.$this->classFormGroup.'">';
        $input .= $label;
        $input .= '<div'.$divClass.'>';
        
        $input .= $this->radioValues($name, $values, $format, $type, $isChecked);
        
        
        $input .= '</div>';
        $input .= '</div>';
        
        return $input;
        
    }
    
    /*
     * chackbox() à les même particularités que radio()
     */
    public function checkbox($name, $options = [], $values =[])
    {
        return $this->radio($name, $options, $values, 'checkbox');
    }
    
    
    
    /*
     * Créer 3 champs pour l'anniversaire : jour - mois - année
     */
    public function birthday($nameDay, $nameMonth, $nameYear , $options = [])
    {
        $labelClass     = isset($options['labelClass']) ? ' class="' .$options['labelClass']. '" ' : '';
        $label          = isset($options['label'])      ? '<label'.$labelClass.'">' .  $options['label'] . '</label>' : '';
        $class          = isset($options['class'])      ? ' class="' .$options['class']. '" ' : '';
        $divClass       = isset($options['divClass']) ? ' class="'. $options['divClass'] .'"' : '';
        
        $input = '<div'. $divClass .'>';
        $input .= '<select class="form-control" name="'. $nameDay .'" '. $class .'>';
        $input .= $this->selectOptions('day', $this->getValue($nameDay));
        $input .= '</select>';
        $input .= '</div>';
        
        $input .= '<div'. $divClass .'>';
        $input .= '<select class="form-control" name="'. $nameMonth .'" '. $class .'>';
        $input .= $this->selectOptions('month', $this->getValue($nameMonth));
        $input .= '</select>';
        $input .= '</div>';
        
        $input .= '<div'. $divClass .'>';
        $input .= '<select class="form-control" name="'. $nameYear .'" '. $class .'>';
        $input .= $this->selectOptions('year', $this->getValue($nameYear));
        $input .= '</select>';
        $input .= '</div>';
                
        return $this->surround($label . $input);
    }
    
    /**
     * 
     * @param array $options
     * @return type string
     */
    public function submit($options = [])
    {
        $labelClass     = isset($options['labelClass']) ? ' class="' .$options['labelClass']. '" ' : '';
        $label          = isset($options['label'])      ? '<label'.$labelClass.'">' .  $options['label'] . '</label>' : '';
        $class          = isset($options['class'])      ? ' class="' .$options['class']. '" ' : '';
        $divClass       = isset($options['divClass'])   ? ' class="'. $options['divClass'] .'"' : '';
        $name           = isset($options['name'])       ? 'id="'. $options['name'] .'" name="'. $options['name'] .'"' : '';
        $value          = isset($options['value'])      ? $options['value'] : 'Button';
                
        $button = $label;
        $button .= '<div'.$divClass.'>';
        $button .= '<button '. $name .' '. $class .'>'. $value .'</button>';
        $button .= '</div>';
        
        return $this->surround($button);
    }
    
}
