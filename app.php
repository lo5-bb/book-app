<?php

require 'vendor/Parsedown.php';
require 'vendor/ParsedownExtra.php';
require 'vendor/ParsedownExtension.php';

class app
{

	/**
	 * Zwraca listę plików md
	 *
	 * @return array
	 */
	public static function getFilesList()
	{
		return json_decode(file_get_contents('book/menu.json'), true);
	}

	private static function getCodeUrl($code) {
		return 'code.lo5.bielsko.pl/'.$code;
	}

	/**
	 * Zwraca treść całej ksiązki
	 *
	 * @return string
	 */
	public static function getFilesContent()
	{
		$files = self::getFilesList();
		$data = '';

		$i = 1;
		foreach ($files as $chapter) {
			$fileData = "\n\n" . trim(file_get_contents('book/'.$chapter['file'])) . "\n\n";
			$html = self::parseMarkdown($fileData);
			$html = self::generateBrowser($html);
			$html = self::generateHeaders($html);
			$html = self::generateBrowserDecode($html);


			$data .= '<section class="chapter">';

			if(!empty($chapter['chapter'])) {
				$data .= '<div class="chapter-no">' . $chapter['chapter'] . '</div>';
			}

			$data .= $html;
			$data .= '</section>' . "\n\n";
		}

		$menu = self::generateTOC($data);

		return array(
			'html' => $data,
			'menu' => $menu
		);
	}

	/**
	 * Konwertuje książkę na język html
	 *
	 * @return string
	 */
	public static function getContent()
	{
		//pobieramy tresc ksiazki
		$data = self::getFilesContent();

		return $data;
	}

	/**
	 * Konwertuje markdown na htmla
	 *
	 * @param $data
	 * @return mixed
	 */
	public static function parseMarkdown($data)
	{

		$Parsedown = new Parsedown_Extension();
		$Parsedown->setUrlsLinked(false);

		$text = $data;

		//$text = self::formatCharacters($text);
		$text = $Parsedown->text($text);
		$text = self::formatSingleCharacters($text);

		return $text;
	}

	private static function formatSingleCharacters($str)
	{
		static $charactersTable;
		if (!isset($charactersTable)) {
			$charactersTable = array(
				'/(\s)(z|i|a|o)\s/' => '$1$2&nbsp;'
			);
		}

		return preg_replace(array_keys($charactersTable), $charactersTable, $str);
	}

	public static function formatCharacters($str)
	{
		static $quotesTable;
		if (!isset($quotesTable)) {
			$quotesTable = array(
				// nested smart quotes, opening and closing
				// note that rules for grammar (English) allow only for two levels deep
				// and that single quotes are _supposed_ to always be on the outside
				// but we'll accommodate both
				// Note that in all cases, whitespace is the primary determining factor
				// on which direction to curl, with non-word characters like punctuation
				// being a secondary factor only after whitespace is addressed.
//				'/\'"(\s|$)/' => '&#8217;&#8221;$1',
//				'/(^|\s|<p>)\'"/' => '$1&#8216;&#8222;',
//				'/\'"(\W)/' => '&#8217;&#8221;$1',
//				'/(\W)\'"/' => '$1&#8216;&#8222;',
//				'/"\'(\s|$)/' => '&#8221;&#8217;$1',
//				'/(^|\s|<p>)"\'/' => '$1&#8222;&#8216;',
//				'/"\'(\W)/' => '&#8221;&#8217;$1',
				//'/(\W)"\'/' => '$1&#8222;&#8216;',
				// single quote smart quotes
//				'/\'(\s|$)/' => '&#8217;$1',
//				'/(^|\s|<p>)\'/' => '$1&#8216;',
//				'/\'(\W)/' => '&#8217;$1',
//				'/(\W)\'/' => '$1&#8216;',
				// double quote smart quotes
//				'/"(\s|$)/' => '&#8221;$1',
//				'/(^|\s|<p>)"/' => '$1&#8222;',
				//'/"(\W)/' => '&#8221;$1',
				//'/(\W)"/' => '$1&#8222;',
				// apostrophes
//				"/(\w)'(\w)/" => '$1&#8217;$2',
				// Em dash and ellipses dots
				'/(\w)\-\-(\w)/' => '$1&#8212;$2',
				'/(\s)\-\-(\s)/' => '$1&#8212;$2',
				'/(\w)\.{3}/' => '$1&#8230;',
			);
		}
		return preg_replace(array_keys($quotesTable), $quotesTable, $str);
	}

	private static function generateTOC($code) {
		$headers = array();

		if (preg_match_all('/<h([1-6]) id="([^"]+)">(.*?)<\/h\\1>/si', $code, $m)) {
			for ($i = 0; $i < count($m[0]); $i++) {
				$headers[] = [
					'no' => $m[1][$i],
					'text' => strip_tags($m[3][$i], '<code>'),
					'url' => $m[2][$i]
				];
			}
		}

		return $headers;
	}

	private static function generateHeaders($code) {
		$code = preg_replace_callback('/<h([1-6])>(.*?)<\/h\\1>/si', function($m){
			return '<h'.$m[1].' id="'.self::urlTitle($m[2]).'">'.$m[2].'</h'.$m[1].'>';
		}, $code);

		return $code;
	}

	private static function generateBrowser($code) {

		$code = preg_replace_callback('/<browser\s+url=(["\'])([^"]+)\\1>(.*?)<\/browser>/siu', function($m){
			return '<browser url="'.$m[2].'">'.htmlspecialchars($m[3]).'</browser>';
		}, $code);

		return $code;
	}

	private static function generateBrowserDecode($code) {
		$code = preg_replace_callback('/<browser\s+url=(["\'])([^"]+)\\1>(.*?)<\/browser>/siu', function($m){
			return '<div class="browser" id="code-'.$m[2].'"><div class="browser-top"><div class="browser-left"><span></span><span></span><span></span></div><div class="browser-right"><div class="browser-url-hamburger"></div></div><div class="browser-url"><div class="browser-url-world"></div>'.self::getCodeUrl($m[2]).'</div></div><div class="browser-content">'.htmlspecialchars_decode($m[3]).'</div></div>';
		}, $code);

		return $code;
	}


	private static function urlTitle($title, $separator = '-')
	{
		$title = self::transliterateToAscii($title);

		$title = preg_replace('![^' . preg_quote($separator) . 'a-z0-9\s]+!', '', strtolower($title));
		$title = preg_replace('![' . preg_quote($separator) . '\s]+!u', $separator, $title);

		return trim($title, $separator);
	}

	private static function transliterateToAscii($str, $case = 0)
	{
		static $UTF8_LOWER_ACCENTS = NULL;
		static $UTF8_UPPER_ACCENTS = NULL;

		$str = strip_tags($str);

		$str = strtr($str, [
			'&nbsp;' => ' ',
			'&lt;' => ' ',
			'&gt;' => ' '
		]);

		if ($case <= 0) {
			if ($UTF8_LOWER_ACCENTS === NULL) {
				$UTF8_LOWER_ACCENTS = array(
					'à' => 'a', 'ô' => 'o', 'ď' => 'd', 'ḟ' => 'f', 'ë' => 'e', 'š' => 's', 'ơ' => 'o',
					'ß' => 'ss', 'ă' => 'a', 'ř' => 'r', 'ț' => 't', 'ň' => 'n', 'ā' => 'a', 'ķ' => 'k',
					'ŝ' => 's', 'ỳ' => 'y', 'ņ' => 'n', 'ĺ' => 'l', 'ħ' => 'h', 'ṗ' => 'p', 'ó' => 'o',
					'ú' => 'u', 'ě' => 'e', 'é' => 'e', 'ç' => 'c', 'ẁ' => 'w', 'ċ' => 'c', 'õ' => 'o',
					'ṡ' => 's', 'ø' => 'o', 'ģ' => 'g', 'ŧ' => 't', 'ș' => 's', 'ė' => 'e', 'ĉ' => 'c',
					'ś' => 's', 'î' => 'i', 'ű' => 'u', 'ć' => 'c', 'ę' => 'e', 'ŵ' => 'w', 'ṫ' => 't',
					'ū' => 'u', 'č' => 'c', 'ö' => 'o', 'è' => 'e', 'ŷ' => 'y', 'ą' => 'a', 'ł' => 'l',
					'ų' => 'u', 'ů' => 'u', 'ş' => 's', 'ğ' => 'g', 'ļ' => 'l', 'ƒ' => 'f', 'ž' => 'z',
					'ẃ' => 'w', 'ḃ' => 'b', 'å' => 'a', 'ì' => 'i', 'ï' => 'i', 'ḋ' => 'd', 'ť' => 't',
					'ŗ' => 'r', 'ä' => 'a', 'í' => 'i', 'ŕ' => 'r', 'ê' => 'e', 'ü' => 'u', 'ò' => 'o',
					'ē' => 'e', 'ñ' => 'n', 'ń' => 'n', 'ĥ' => 'h', 'ĝ' => 'g', 'đ' => 'd', 'ĵ' => 'j',
					'ÿ' => 'y', 'ũ' => 'u', 'ŭ' => 'u', 'ư' => 'u', 'ţ' => 't', 'ý' => 'y', 'ő' => 'o',
					'â' => 'a', 'ľ' => 'l', 'ẅ' => 'w', 'ż' => 'z', 'ī' => 'i', 'ã' => 'a', 'ġ' => 'g',
					'ṁ' => 'm', 'ō' => 'o', 'ĩ' => 'i', 'ù' => 'u', 'į' => 'i', 'ź' => 'z', 'á' => 'a',
					'û' => 'u', 'þ' => 'th', 'ð' => 'dh', 'æ' => 'ae', 'µ' => 'u', 'ĕ' => 'e', 'ı' => 'i',
				);
			}
			$str = str_replace(
				array_keys($UTF8_LOWER_ACCENTS),
				array_values($UTF8_LOWER_ACCENTS),
				$str
			);
		}
		if ($case >= 0) {
			if ($UTF8_UPPER_ACCENTS === NULL) {
				$UTF8_UPPER_ACCENTS = array(
					'À' => 'A', 'Ô' => 'O', 'Ď' => 'D', 'Ḟ' => 'F', 'Ë' => 'E', 'Š' => 'S', 'Ơ' => 'O',
					'Ă' => 'A', 'Ř' => 'R', 'Ț' => 'T', 'Ň' => 'N', 'Ā' => 'A', 'Ķ' => 'K', 'Ĕ' => 'E',
					'Ŝ' => 'S', 'Ỳ' => 'Y', 'Ņ' => 'N', 'Ĺ' => 'L', 'Ħ' => 'H', 'Ṗ' => 'P', 'Ó' => 'O',
					'Ú' => 'U', 'Ě' => 'E', 'É' => 'E', 'Ç' => 'C', 'Ẁ' => 'W', 'Ċ' => 'C', 'Õ' => 'O',
					'Ṡ' => 'S', 'Ø' => 'O', 'Ģ' => 'G', 'Ŧ' => 'T', 'Ș' => 'S', 'Ė' => 'E', 'Ĉ' => 'C',
					'Ś' => 'S', 'Î' => 'I', 'Ű' => 'U', 'Ć' => 'C', 'Ę' => 'E', 'Ŵ' => 'W', 'Ṫ' => 'T',
					'Ū' => 'U', 'Č' => 'C', 'Ö' => 'O', 'È' => 'E', 'Ŷ' => 'Y', 'Ą' => 'A', 'Ł' => 'L',
					'Ų' => 'U', 'Ů' => 'U', 'Ş' => 'S', 'Ğ' => 'G', 'Ļ' => 'L', 'Ƒ' => 'F', 'Ž' => 'Z',
					'Ẃ' => 'W', 'Ḃ' => 'B', 'Å' => 'A', 'Ì' => 'I', 'Ï' => 'I', 'Ḋ' => 'D', 'Ť' => 'T',
					'Ŗ' => 'R', 'Ä' => 'A', 'Í' => 'I', 'Ŕ' => 'R', 'Ê' => 'E', 'Ü' => 'U', 'Ò' => 'O',
					'Ē' => 'E', 'Ñ' => 'N', 'Ń' => 'N', 'Ĥ' => 'H', 'Ĝ' => 'G', 'Đ' => 'D', 'Ĵ' => 'J',
					'Ÿ' => 'Y', 'Ũ' => 'U', 'Ŭ' => 'U', 'Ư' => 'U', 'Ţ' => 'T', 'Ý' => 'Y', 'Ő' => 'O',
					'Â' => 'A', 'Ľ' => 'L', 'Ẅ' => 'W', 'Ż' => 'Z', 'Ī' => 'I', 'Ã' => 'A', 'Ġ' => 'G',
					'Ṁ' => 'M', 'Ō' => 'O', 'Ĩ' => 'I', 'Ù' => 'U', 'Į' => 'I', 'Ź' => 'Z', 'Á' => 'A',
					'Û' => 'U', 'Þ' => 'Th', 'Ð' => 'Dh', 'Æ' => 'Ae', 'İ' => 'I',
				);
			}
			$str = str_replace(
				array_keys($UTF8_UPPER_ACCENTS),
				array_values($UTF8_UPPER_ACCENTS),
				$str
			);
		}
		return $str;

	}
}