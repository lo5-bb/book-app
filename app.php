<?php

class app {

	public static function getFilesList() {
		return glob('book/*.md');
	}

	public static function getFilesContent() {
		$files = self::getFilesList();
		$data = '';

		foreach($files as $file) {
			$data .= trim(file_get_contents($file))."\n\n";
		}

		return trim($data);
	}

	public static function getContent() {
		$data = self::getFilesContent();

		return nl2br($data);

	}

}