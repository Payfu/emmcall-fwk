<?php
namespace core\Tools;

/**
 * Fonctions diverses et utiles
 * @author EmmCall
 */
class Arrays
{
  /*
   * Réorganise un tableau en fonction de ses index
   * ex:
   * $array = array(0 => 'a', 1 => 'b', 2 => 'c', 3 => 'd');
   * $newOrderArray = array(3, 2, 0, 1);
   * return : Array([3] => d [2] => c [0] => a [1] => b)
   */
  public static function reorderArray(array $array, array $newOrderArray, array &$c = []) : array{
    foreach($newOrderArray as $index) {
      $c[$index] = $array[$index];
    }
    return $c;
  }
}
