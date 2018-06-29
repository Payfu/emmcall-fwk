<?php
namespace Core\Login;

class Login extends Token
{
 
 private $_instanceToken;
 
 public function __construct()
 {
  
  if(is_null($this->_instanceToken)){ $this->_instanceToken = new Token(); }
 }
  
 public function getToken() : string {		
  return $this->_instanceToken->generateToken();		
 }
}
