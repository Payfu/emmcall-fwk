<?php
declare(strict_types=1);

/**
 * 13-09-2016 : Création de OpenGraph
 */

namespace Core\OpenGraph;

/**
 *
 * Description
 * Implémentation d'OpenGraph : http://ogp.me/
 * Twitter : https://dev.twitter.com/cards/markup
 * http://qnimate.com/open-graph-protocol-in-facebook/
 * https://developers.facebook.com/docs/sharing/opengraph/object-properties
 */
class OpenGraph
{
	/*
	 * Les différents format : Open Graph, Facebook, Twitter
	 */
	public function og ($data = array()) : string{
		return SELF::checkData($data);
	}
	
	public function fb ($data = array()) : string
	{
		return SELF::checkData($data);
	}
	
	/*
	 * Création des META
	 */
	private function checkData (array $data, $target) : string
	{
		$str  = '';
		foreach ($data as $property => $content)
		{
			if($target == 'og' or $target == 'fb'){$str .= "\r\t".'<!--Open Graph-->';   }
			
			if (SELF::allProperty($property, $target))
			{
				$str .="\r\t".'<meta property="'.$target.':'.$property.'" content="'.$content.'" />';
			}
			else
			{
				$str .= "\r\t".'<!--ERROR : Open Graph-->';
			}
		}
	}
	
	/*
	 * Les différentes propriété en fonction de l'api
	 */
	private function allProperty($property, $target) : bool
	{
		// Open Graph
		if($target == 'og'){
			$propertyList = [
				"type", // website, article, video, book, music, profile -> Et leur sous-type video.movie, video:width etc.
				"title", // Titre apparaissant dans la preview
				"site_name", // Nom du site
				"url", // Url de la page, on peut doubler cet argument si 2 url pointe vers la même page
				"description", // Description de la page
				"image", // url de l'image de preview, FB recommande minimum 1200*630 (max 5Mo)
				"video", // url de la vidéo qui pourra éventuellement être jouée dans FB
				"audio", // url du mp3 qui pourra éventuellement être joué dans FB
				"see_also", // url des autres articles traitants de ce sujet, on peut doubler cet argument
				"locale", // La langue pricipale : fr_FR
				"locale:alternate", // La lange secondaire : en_En
			];
		}
		
		// Facebook
		if($target == 'fb'){
			$propertyList = [
				"app_id", // id de l'application
				"admins", // id utilisateur, on peut mettre plusieurs comptes ex : content="557880802, 557099992"
			];
		}
		
		// Twitter
		/*
		twitter:title: Same as "og:title".
twitter:url: Sames as "ug:url".
twitter:site: The website twitter account.
twitter:creator: From the user (post author) Twitter account.
twitter:description: Same as "og:description".
twitter:image:src: Same as "og:image".
twitter:card:src: With value "summary_large_image" or "summary".*/
		
		
		return in_array($property, $propertyList);
	}
}

