<?php
namespace core\Tools;

/**
 * Fonctions diverses et utiles
 * @author EmmCall
 */
class Emails
{
  /*
   *  Fonction mail
   * $to : email du contact du site
   * $from : email de la personne qui contact le site
   * $subject : l'objet du message
   */
  public static function sendEmail($emailTo, $from, $subject, $message) : bool
  {
    $rn = "\n"; // Passage à la ligne (normalement on \r\n mais hotmail crée un brug en convertissant le \n en \r\n)

    $headers =  'From: '. $from . $rn .
                'Reply-To: '. $from . $rn .
                'X-Mailer: PHP/' . phpversion();


    return mail($emailTo, $subject, $message, $headers);
  }
}
