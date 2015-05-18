<?php

class Parsedown_Extension extends Parsedown
{
	function __construct()
	{
		$this->BlockTypes['('] [] = 'Snippet';
		$this->BlockTypes['/'] [] = 'Todo';
	}

	protected function blockSnippet($Excerpt)
	{
		$text = 'Zobacz na kod <strong>%s</strong>';

		if (preg_match('/^\(kod\s([0-9\.]+)\)/i', $Excerpt['text'], $matches)) {
			return array(
				'extent' => strlen($matches[0]),
				'element' => array(
					'name' => 'div',
					'text' => sprintf($text, $matches[1]),
					'attributes' => array(
						'class' => 'snippet',
					),
				),
			);
		}
	}

	protected function blockTodo($Excerpt) {

		if (preg_match('#^\/\/(\!)*?(.*)\s?(?:\(([\w\s]+)\))?\s*$#iuU', $Excerpt['text'], $matches)) {
			return array(
				'extent' => strlen($matches[0]),
				'element' => array(
					'name' => 'div',
					'text' => $matches[2].(!empty($matches[3]) ? '<cite class="autor">'.$matches[3].'</cite>' : ''),
					'attributes' => array(
						'class' => 'todo'.(($matches[1] == '!') ? ' important' : '')
					),
				),
			);
		}
	}
}