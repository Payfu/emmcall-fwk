<?php
namespace Core\Tools;


/**
 * Fonctions diverses et utiles
 *
 * 16-06-2016 Ajout de keepWords()
 * 08-06-2016 Ajout de sendEmail()
 * 03-06-2016 Ajout de removeSpecChar()
 * 18-04-2016 Ajout de formatDateTime()
 * 
 *
 * @author EmmCall
 */
class Tools
{
    /**
     * Suppression des accents
     * @param string $str
     * @param string $charset
     * @return string
     */
    public function removeAccents($str, $charset='utf-8') : string
    {
        // transformer les caractères accentués en entités HTML
        $str = htmlentities($str, ENT_NOQUOTES, $charset);
        
        // remplacer les entités HTML pour avoir juste le premier caractères non accentués
        // Exemple : "&ecute;" => "e", "&Ecute;" => "E", "Ã " => "a" ...
        $str = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
        // Remplacer les ligatures tel que : Œ, Æ ...
        // Exemple "Å“" => "oe"
        $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); // pour les ligatures e.g. '&oelig;'
        
        // Supprimer tout le reste
        $str = preg_replace('#&[^;]+;#', '', $str); // supprime les autres caractères

        return $str;
    }

    /*
     * Suppression de TOUS les caractères spéciaux (accent inclus), on ne garde que les alphanum 
     * $replaceBy est ce qui remplacera les caractères speciaux (espace vide par défaut) et on supprime les éventuels doublons (ex: --- => -)
     */
    public function removeSpecChar($str, $replaceBy=false) : string
    {
        $rep = $replaceBy ?? '';
        $str = preg_replace("#[^a-zA-Z0-9]#", $rep, $str);
        if($replaceBy){ $str = preg_replace('#(?:(['.$rep.'])\1)\1*#', $rep, $str); }
        return $str; 
    }


    /*
     * On ne garde que les mots qui ont au minimum la taille $length (défaut 4)
     */
    public function keepWords($str, $length=4, $delimiter=' ') : string
    {
        $strSanitize = '';
        foreach (explode($delimiter, $str) as $value) {
            if(strlen($value) >= $length){ $strSanitize .= $delimiter.$value;}
        }
        return substr($strSanitize, 1);
    }

    /*
        On format une date Ymd -> dmY
    */
    public function formatDateTime($str, $select=null)
    {
        $datetime = explode(' ', $str);

        list($year, $month, $day) = preg_split('/[-\/.]/', $datetime[0]);
        
        return $day.'-'.$month.'-'.$year;
    }

    /*
     *  Fonction mail
     * $to : email du contact du site
     * $from : email de la personne qui contact le site
     * $subject : l'objet du message
     */

    public function sendEmail($emailTo, $from, $subject, $message) : bool
    {
        $rn = "\n"; // Passage à la ligne (normalement on \r\n mais hotmail crée un brug en convertissant le \n en \r\n)

        
        $headers =  'From: '. $from . $rn .
                    'Reply-To: '. $from . $rn .
                    'X-Mailer: PHP/' . phpversion();


        return mail($emailTo, $subject, $message, $headers);
    }

    /*
     * On check un dossier et on regarde son contenu
     * Il retourne une valeur de 0 à 2
     * $path = nécessite un chemin ABSOLU => ROOT
     */
    public function ctrlFolder($path) : int
    {
        $fichierTrouve=0;
        if (is_dir($path) and $dh = opendir($path))
        {
            while (($file = readdir($dh)) !== false && $fichierTrouve==0){ if ($file!="." && $file!=".." ) { $fichierTrouve=1;} }
            closedir($dh);             
        }
        // Le répertoir n'existe pas
        elseif(!is_dir($path))                           {             $val = 0;     }
        // Le répertoire existe mais il est vide
        if(is_dir($path) and $fichierTrouve == 0)        {             $val = 1;     }
        // Le répertoire contient des fichiers
        if(is_dir($path) and $fichierTrouve == 1)        {             $val = 2;     }
        
        return $val; 
    }


    /*
     * On supprime le contenu d'un dossier, retour true si l'action réussie
     */
    public function viderDossier($path) : bool
    {
        $var = false;
        if($dh = opendir($path))
        {            
            // On lit chaque fichier du répertoire dans la boucle.
            while (false !== ($file = readdir($dh))) 
            {
                // Si le fichier n'est pas un répertoire…
                // On efface le fichier
                if ($file != ".." AND $file != "." AND !is_dir($file)){ unlink($path.$file); }
            }
            $var = true;
            closedir($dh); 
        }

        return $var;
    }
}
