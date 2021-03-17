<?php
namespace core\Tools;

class imageProcess {
  private $folderAvatar = ROOT.'/public/img/vignette/'; // Dossier de l'avatar : le chemin absolu doit être respecté.
  private $folderImgBg = ROOT.'/public/img/background/';
  private $folderImgGalerie = ROOT.'/public/img/galerie/';
  private $folderImgWatermark = ROOT.'/public/img/watermark/';
  private $watermarkOpacity = 20; // Opacité du watermak
  private $watermarkPosition = 'bas-gauche';
  private $extension = ["jpg", "jpeg", "png"]; // Liste des extensions authorisées
  private $maxsize = '1024' * 1000 * 30; // 1ko * 1000 * le nombre de Mo authorisé
  private $weightAvatar = '180'; // C'est un carré
  private $weightImg = '700'; // Largeur max de la photo
  private $borderMax = '900'; // On recadre avec la bordure la plus large 
  private $compressionAvatar = '50'; // Compression de l'image en %
  private $compressionImg = '75'; // Compression de l'image en %
  private $nameAvatar;
  private $nameImg;
  private $error; // Code erreur

  /*
    * Upload d'une image de profile 
    * par défaut le format est carré
    *
    * name, type, tmp_name, error, size
    */
  public function uploadAvatar($files, $nameFile = false)
  {
    if(SELF::controleFile($files))
    {
      $this->nameAvatar = $nameFile ? SELF::renameFile($files, $nameFile) : $files['name'];
      return SELF::avatar($files);
    }
    else
    {
      return false;
    }
  }


    /*
     * Upload classic
     */
    private function upload($files, $dest)
    {
    	return move_uploaded_file($files['tmp_name'], $dest);
    }

    /*
     * Contrôle s'il y a une erreur lors du chargement, si l'extension ou la taille sont correct.
     */
    private function controleFile($files) : bool
    {
    	$var = true;
        // S'il y a une erreur lors de l'upload
    	if(empty($files) || $files['error'] > 0){ $var = false;	$err = 1;}

        // Si l'extension est correcte
    	$ext = strtolower(substr(strrchr($files['name'], '.'), 1));
    	if($this->extension != '' && !in_array($ext, $this->extension) ){	$var = false; $err = 2;	}

        // Si le poids max n'est pas dépassé
    	if($files['size'] > $this->maxsize ){	$var = false; $err = 3;  	}


        // Si l'image est au format demandé : Portrait ou Paysage

        // Si le bord le plus large de l'image n'est pas inférieur à la taille minimum autorisée 

        if($err > 0){ $this->error = $err;}

    	return $var;
    }

    /* 
     *  Les divers code erreur
     */
    private function msgError($numError) : string
    {
        $msg = [
            "1"=>"Une erreur est survenue lors du chargement de l'image",
            "2"=>"L'image doit être au format .JPG, .JPEG ou .PNG",
            "3"=>"Le poids de l'image ne doit pas dépasser".$this->maxsize / (1024 * 1000)."Mo",
            "4"=>"",
            "5"=>""
        ];
        return $msg["$numError"];
    }


    /*
     * On renome le fichier
     */
    private function renameFile($files, $newName) : string
    {
    	$ext = strtolower( strrchr($files['name'], '.') );
		return $newName.$ext;
    }

    /*
     * La vignette est créer à partir de l'image dans le dossier background $folderImgBg (payfu.fr)
     */
    public function vignette($name, $prefix=null) : bool
    {
        $cheminTmp = $this->folderImgBg;
        $cheminVignette = $this->folderAvatar;
        $largeurVignette = $this->weightAvatar;

        // On récupère la photo
        $imgResize = imagecreatefromjpeg($cheminTmp.$name.'.jpg');
        $largeurImg = imagesx($imgResize);
        $hauteurImg = imagesy($imgResize);
        
        // On prend le bord le moins large $bordMoinsL et le bord le plus large $bordPlusLarge
        if($largeurImg > $hauteurImg)       { $bordMoinsL = $hauteurImg; $bordPlusL = $largeurImg; $calcul = "ok"; $axe = 'x'; }
        elseif($largeurImg < $hauteurImg)   { $bordMoinsL = $largeurImg; $bordPlusL = $hauteurImg; $calcul = "ok"; $axe = 'y';}
        elseif($largeurImg == $hauteurImg)  { $bordMoinsL = $largeurImg; $bordPlusL = $largeurImg; $calcul = "ko"; $axe = 'x';}
                
        // ($bordPlusLarge - $bordMoinsL)/2 = la $marge de départ pour couper un carré dans le milieu de l'image 
        if($calcul == 'ok'){ $marge = round(($bordPlusL - $bordMoinsL)/2); }else{ $marge = 0; }
        
        // On crop 
        //imagecopy($sortie, $entree, bordure_x, bordure_y,  l'image démarre sur x, l'image démarre sur y, la place que prend l'image sur x, la place que prend l'image sur y);
        $imgCrop = imagecreatetruecolor($bordMoinsL, $bordMoinsL); // C'est un carré
        
        if($axe == 'x'){ $axe_x = $marge; }else{ $axe_x = 0; }
        if($axe == 'y'){ $axe_y = $marge; }else{ $axe_y = 0; }
        
        // On crop la photo par son milieu en vignette 64*64px
        imagecopy($imgCrop, $imgResize, 0, 0, $axe_x, $axe_y, $bordMoinsL, $bordMoinsL);
        
        $largeurImgCrop = imagesx($imgCrop);
        $hauteurImgCrop = imagesy($imgCrop);
        
        // On resize le crop en vignette
        $imgCropV = imagecreatetruecolor($largeurVignette, $largeurVignette);
        imagecopyresampled($imgCropV, $imgCrop, 0, 0, 0, 0, $largeurVignette, $largeurVignette, $largeurImgCrop, $hauteurImgCrop);
        
        // On enregistre l'image dans le dossier
        return imagejpeg($imgCropV, $cheminVignette.$prefix.$name.'.jpg', $this->compressionAvatar ); 
      
    }


    /*
     *	Format Carré
     */ 
    private function avatar($files) : bool
    {
    	
    	$largeurVignette = $this->weightAvatar;


    	$ext = strtolower( substr(strrchr($files['name'], '.'), '1') );
    	$imgTmpPath = $files['tmp_name'];

    	// On récupère la photo
    	if($ext === 'jpg' || $ext ==='jpeg'){ $imgTmp = imagecreatefromjpeg($imgTmpPath); }
    	elseif($ext ==='png'){    		$imgTmp = imagecreatefrompng($imgTmpPath);    	}

        $largeurImg = imagesx($imgTmp);
        $hauteurImg = imagesy($imgTmp);

        // On prend le bord le moins large $bordMoinsL et le bord le plus large $bordPlusLarge
        if($largeurImg > $hauteurImg)       { $bordMoinsL = $hauteurImg; $bordPlusL = $largeurImg; $calcul = true;  $axe = 'x'; }
        elseif($largeurImg < $hauteurImg)   { $bordMoinsL = $largeurImg; $bordPlusL = $hauteurImg; $calcul = true;  $axe = 'y'; }
        elseif($largeurImg == $hauteurImg)  { $bordMoinsL = $largeurImg; $bordPlusL = $largeurImg; $calcul = false; $axe = 'x'; }

        // ($bordPlusLarge - $bordMoinsL)/2 = la $marge de départ pour couper un carré dans le milieu de l'image 
        if($calcul){ $marge = round(($bordPlusL - $bordMoinsL)/2); }else{ $marge = 0; }

        // On crop 
        //imagecopy($sortie, $entree, bordure_x, bordure_y,  l'image démarre sur x, l'image démarre sur y, la place que prend l'image sur x, la place que prend l'image sur y);
        $imgCrop = imagecreatetruecolor($bordMoinsL, $bordMoinsL); // C'est un carré
        
        if($axe == 'x'){ $axe_x = $marge; }else{ $axe_x = 0; }
        if($axe == 'y'){ $axe_y = $marge; }else{ $axe_y = 0; }

        // On crop la photo par son milieu en vignette x*xpx
        imagecopy($imgCrop, $imgTmp, 0, 0, $axe_x, $axe_y, $bordMoinsL, $bordMoinsL);
        
        $largeurImgCrop = imagesx($imgCrop);
        $hauteurImgCrop = imagesy($imgCrop);

        // On resize le crop en vignette
        $imgCropV = imagecreatetruecolor($largeurVignette, $largeurVignette);
        imagecopyresampled($imgCropV, $imgCrop, 0, 0, 0, 0, $largeurVignette, $largeurVignette, $largeurImgCrop, $hauteurImgCrop);

        // On enregistre l'image dans le dossier au format JPEG
        if(imagejpeg($imgCropV, $this->folderAvatar.$this->nameAvatar, $this->compressionAvatar))
        {
        	// On libère la mémoire
        	imagedestroy($imgCropV);
        	return true;
        }
        else{
        	return false;
        }    
    }

    /*
     *  On redimmensionne en tenant compte du bord le plus large
     */
    public function resize($name, $files) : bool
    {
        $BordLePlusLargePredefinie = $this->borderMax;


        /*
         *  On charge d'abord les images avec la bibliothèque GD
         */
        // Récupération de la photo via son nom et son dossier
        $imgTmpPath = $files['tmp_name'];
        $imageFull = imagecreatefromjpeg($imgTmpPath);
        
        $largeurImg = imagesx($imageFull);
        $hauteurImg = imagesy($imageFull);
        
        
        
        /*
         * On calcul la proportion
         */
        
        // Si la largeur de la photo est supérieur à la hauteur
        if($largeurImg > $hauteurImg)
        {            
            //$proportion = ceil($largeurImg/$BordLePlusLarge);
            $proportion = $largeurImg/$BordLePlusLargePredefinie;
            $hauteurPredefini = round($hauteurImg/$proportion);
            $largeurPredefini = $BordLePlusLargePredefinie;
        }
        
        // Si la hauteur de la photo est supérieur à la largeur
        elseif($largeurImg < $hauteurImg)
        { 
            //$proportion = ceil($hauteurImg/$BordLePlusLarge);
            $proportion = $hauteurImg/$BordLePlusLargePredefinie;
            $largeurPredefini = round($largeurImg/$proportion);
            $hauteurPredefini = $BordLePlusLargePredefinie;
        }
        
        // Si la hauteur de la photo est églal à la largeur
        elseif($largeurImg == $hauteurImg)
        {
            $hauteurPredefini = $BordLePlusLargePredefinie;
            $largeurPredefini = $BordLePlusLargePredefinie;
        }
        
        // On crée l'image resizée vide
        $imgResize = imagecreatetruecolor($largeurPredefini, $hauteurPredefini); 
        
        // On crée l'image resizée finale
        imagecopyresampled($imgResize, $imageFull, 0, 0, 0, 0, $largeurPredefini, $hauteurPredefini, $largeurImg, $hauteurImg);
        
        // On enregistre l'image dans le dossier
        return imagejpeg($imgResize, $this->folderImgGalerie.$name.'.jpg' );
    }

    /*
     * Placement du logo avec choix du positionnement
     */
    public function placementLogo($name, $nameLogo) : bool
    {

        $cheminTmp = $this->folderImgGalerie;
        $cheminWatermark = $this->folderImgWatermark;
        $opacity = $this->watermarkOpacity;
        $placementLogo = $this->watermarkPosition; 

        // Initisalisation
        $destination_x = $destination_y = 0;
        
        // Récupération de l'image resizée et watermarkée
        $imgResize = imagecreatefromjpeg($cheminTmp.$name.'.jpg');
        $largeurImg = imagesx($imgResize);
        $hauteurImg = imagesy($imgResize);
        
        // Récupération du Logo
        $sourceLogo = imagecreatefrompng($cheminWatermark.$nameLogo.'.png');
        $largeur_sourceLogo = imagesx($sourceLogo);
        $hauteur_sourceLogo = imagesy($sourceLogo);
        
        if($placementLogo == 'centre')
        {
            $milieuLogo_x = $largeur_sourceLogo/2;
            $milieuLogo_y = $hauteur_sourceLogo/2;

            $destination_x = ($largeurImg /2)-$milieuLogo_x;
            $destination_y = ($hauteurImg /2)-$milieuLogo_y;
        }
        elseif($placementLogo == 'haut-gauche')
        {
            $destination_x = 40;
            $destination_y = 20;
        }
        elseif($placementLogo == 'haut-droite')
        {
            $destination_x = ($largeurImg-$largeur_sourceLogo) - 40;
            $destination_y = 20;
        }
        elseif($placementLogo == 'bas-gauche')
        {
            $destination_x = 40;
            $destination_y = ($hauteurImg-$hauteur_sourceLogo) - 20;
        }
        elseif($placementLogo == 'bas-droite')
        {
            $destination_x = ($largeurImg-$largeur_sourceLogo) - 40;
            $destination_y = ($hauteurImg-$hauteur_sourceLogo) - 20;
        }
        
        imagecopymerge($imgResize, $sourceLogo, $destination_x, $destination_y, 0, 0, $largeur_sourceLogo, $hauteur_sourceLogo, $opacity);
        
        // On enregistre l'image dans le dossier
        return imagejpeg($imgResize, $cheminTmp.$name.'.jpg' );
    }

    /*
     * On recrée une image à partir d'un encodage de base64 (jpg ou png)
     */
    public function base64Img($dataFull, $nameImg, $ext = 'jpeg') : bool
    {
        $rtn = false;
        // On retire "data: image/jpg;base46," et on décode
        $data = base64_decode(substr(strrchr($dataFull, ","), 1));
        
        $im = imagecreatefromstring($data);

        if ($im !== false) {
            
            if($ext === 'jpg' or $ext === 'jpeg')
            {
                imagejpeg($im, $this->folderImgBg.$nameImg.'.jpg');
                $rtn = true;
            }
            elseif($ext === 'png')
            {
               imagepng($im, $this->folderImgBg.$nameImg.'.png');
                $rtn = true; 
            }
            
            imagedestroy($im);
        }

        return $rtn;
    }

    


}