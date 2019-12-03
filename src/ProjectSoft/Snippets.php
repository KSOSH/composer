<?php
namespace ProjectSoft;

class Snippets {

	public static function fullUrl(\DocumentParser $modx, int $id = 0)
	{
		$url = "";
		$str = html_entity_decode($_SERVER['QUERY_STRING']);
		parse_str($str, $arr);
		unset($arr["q"]);
		unset($arr["hash"]);
		//Util::debug($arr);
		$query = "";
		if(count($arr))
			$query = "?" . http_build_query($arr, '', "&");
		$url = $modx->makeUrl($id,'','', 'full');
		return $url;
	}
	
	public static function dataID()
	{
		$modx = evolutionCMS();
		$id = $modx->documentObject["id"];
		$url = "<meta property=\"data:id\" content=\"" . $modx->config['site_url'] . $id;
		$str = html_entity_decode($_SERVER['QUERY_STRING']);
		parse_str($str, $arr);
		unset($arr["q"]);
		unset($arr["hash"]);
		$query = "";
		if(count($arr))
			$query = "?" . http_build_query($arr, '', "&");
		//$url .= $query;
		return $url . "\" />";
	}

	public static function ogImage(\DocumentParser $modx)
	{
		$object = $modx->documentObject;
		$ID = $object['id'];
		$img = $object['imgSoc'][1];
		$out = "";
		$type = (isset($object['gallery']) || isset($object['advance']));
		
		if(isset($object['gallery'])):
			$display = isset($_GET["advance"]) ? $modx->config["display_advance"] : "all";
			$json = $modx->runSnippet('multiTV', array(
				"tvName"		=> "gallery",
				"docid"			=> $ID,
				"display"		=> $display,
				"paginate"		=> 1,
				"reverse"		=> 1,
				"toJson"		=> 1
			));
			$json = json_decode($json);
			if(isset($json[0])):
				$img = $json[0]->image;
			endif;
			if(isset($_GET["gallery"]) || isset($_GET["advance"])):
				$i = isset($_GET["gallery"]) ? (int)$_GET["gallery"] - 1 : (isset($_GET["advance"]) ? (int)$_GET["advance"] - 1 : 0);

				if(isset($json[$i])):
					$img = $json[$i]->image;
				endif;
			endif;
		endif;
		$img = (string)Util::trimBasePath($img);
		if(is_file(MODX_BASE_PATH . $img)):
			$og_1 = $modx->runSnippet('thumb', array(
				'input'		=> $img,
				'options'	=> 'w=537,h=240,f=jpg,zc=C'
			));
			$og_2 = $modx->runSnippet('thumb', array(
				'input'		=> $img,
				'options'	=> 'w=400,h=400,f=jpg,zc=C'
			));
			$out = '<meta itemprop="image" content="[(site_url)]' . $og_1 . '" />';
			$out .= '<meta property="og:image" content="[(site_url)]' . $og_1 . '" />';
			$out .= '<meta property="og:image:width" content="537" />';
			$out .= '<meta property="og:image:height" content="240" />';
			$out .= '<meta property="og:image:type" content="image/jpeg" />';
			$out .= '<meta property="og:image" content="[(site_url)]' . $og_2 . '" />';
			$out .= '<meta property="og:image:width" content="400" />';
			$out .= '<meta property="og:image:height" content="400" />';
			$out .= '<meta property="og:image:type" content="image/jpeg" />';
			$out .= '<meta property="twitter:image0" content="[(site_url)]' . $img . '" />';
			$out .= '<meta property="twitter:image1" content="[(site_url)]' . $og_1 . '" />';
			$out .= '<meta property="twitter:image2" content="[(site_url)]' . $og_2 . '" />';
			return $out;
		endif;
		return $out;
	}
}
$modx = evolutionCMS();
$modx->addSnippet('dataID', '\ProjectSoft\Snippets::dataID');