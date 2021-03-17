<?php
namespace Core\Controller;

/**
 * Description of TableController
 *
 * @author emmanuel.callec
 */
class TableController
{
  protected function debug($sql, $attributes){
    echo "<pre>";
    print_r($sql);
    echo "</pre>";
    
    echo "<pre>";
    print_r($attributes);
    echo "</pre>";
    exit();
  }
}
