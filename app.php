<?php

require 'vendor/Parsedown.php';
require 'vendor/ParsedownExtra.php';

class app {

	/**
	 * Zwraca listę plików md
	 *
	 * @return array
	 */
	public static function getFilesList() {
		return glob('book/*.md');
	}

	/**
	 * Zwraca treść całej ksiązki
	 *
	 * @return string
	 */
	public static function getFilesContent() {
		$files = self::getFilesList();
		$data = '';

		foreach($files as $file) {
			$data .= trim(file_get_contents($file))."\n\n";
		}

		return trim($data);
	}

	/**
	 * Konwertuje książkę na język html
	 *
	 * @return string
	 */
	public static function getContent() {
		//pobieramy tresc ksiazki
		$data = self::getFilesContent();

		$data = self::parseMarkdown($data);

		return $data;
	}

	/**
	 * Konwertuje markdown na htmla
	 *
	 * @param $data
	 * @return mixed
	 */
	public static function parseMarkdown($data) {

		$Parsedown = new Parsedown();

		return $Parsedown->text($data);
	}
}