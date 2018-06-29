<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Core\Error;

/**
 * Description of Errors
 *
 * @author dev.hrteam
 */
class Errors
{
  public function __construct()
  {
    set_error_handler([$this,'myerrorHandler']);
  }
  
  public function getError(){
    
  }
  
  
  private function myerrorHandler($errno,$errmsg,$errfile) {        
        //email yourself the error message and code
        $msg  =   "An error {$errno} occured on page ".$_SERVER['REQUEST_URI'].", in the file {$errfile}.<br /><br />The error is shown below:<br /><strong>{$errmsg}</strong>";
        
        echo $msg;
        exit();     
    }
}
