<?php
namespace Core\Tools;

/**
 * Description of mail
 *
 * @author guillaume.dauchez
 */
class phpMailer 
{
  private $_mail;
  private $_host;
  private $_fromEmail;
  private $_fromName;
  private $_port;
  private $_bcc;
  
  
  public function __construct($host = "smtp.gmail.com", $fromEmail = 'no-reply@domaine.com', $fromName = "", $port = 587) 
  {
    //Instanciation de la classe PHP Mailer
    $this -> _mail = new \PHPMailer\PHPMailer\PHPMailer();    
    
    $this -> _host = $host;
    $this -> _fromEmail = $fromEmail;
    $this -> _fromName = $fromName;
    $this -> _port = $port;
    //$this -> _bcc = $bcc;
  }
  
  public function sendMail($subject, $body, $to, $BCC = null, $files = array())
  {			
    
    if(is_array($to) && count($to) > 0):
    
      $mail = $this -> _mail;

      $mail->CharSet = 'UTF-8';

      //Definition du type de debogage
      $mail->SMTPDebug = 4;

      //Definitiion du serveur smtp
      $mail->Host = $this -> _host;  // Specify main and backup SMTP servers

      //Definition du nom de l expediteur
      //$mail->Username = $this -> username;

      //Definition du port
      $mail->Port = $this -> _port;                                    

      //Indique si on utilise l'HTML dans le mail
      $mail->isHTML(true);      

      //Definition de l'adresse de l'expediteur
      $mail->setFrom($this -> _fromEmail, $this -> _fromName);

      //Definition de l'adresse de destination        
      foreach($to as $name => $email):
        $mail->addAddress($email, $name);
      endforeach;          
    
      //Ajout d'une copie cachee
      if(is_array($BCC) && count($BCC) > 0):        
        foreach($BCC as $name => $email):
          $mail->addBCC($email, $name);		
        endforeach;        
      endif;
    
      //Ajout des eventuels fichiers joints    
      if(is_array($files) && count($files) > 0):
      
        foreach($files as $file):
          $url = $file["url"];  
          $mail -> addAttachment($file);
        endforeach;
            
      endif;

      //Definition du sujet
      $mail->Subject = $subject;

      //Definition du message
      $mail->Body    = $body;

      //Definition du message si l'HTML n'est pas supporte
      $mail->AltBody = 'Votre lecteur de courrier ne lit pas le HTML. ';

      //Envoi du mail
      if (!$mail->send()) :
        echo 'Erreur de Mailer : ' . $mail->ErrorInfo;
      endif;
      
    endif;
  }
}
