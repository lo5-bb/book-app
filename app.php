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
		return glob('book/*.md');
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

		foreach ($files as $file) {
			$fileData = '  '.trim(file_get_contents($file)).'  ';
			$html = self::parseMarkdown($fileData);
			$data .= '<section class="chapter">'.$html.'</section>'. "\n\n";
		}

		return trim($data);
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

		$text = $data;

		$text = self::formatCharacters($text);
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

	public static function generateTOC($code)
	{
		return '';
	}
}