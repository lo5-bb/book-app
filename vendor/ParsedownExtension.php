<?php

class Parsedown_Extension extends Parsedown
{
	function __construct()
	{
		$this->BlockTypes['('] [] = 'Snippet';
	}

	protected function blockSnippet($Excerpt)
	{
		$text = 'Zobacz na kod <strong>%s</strong>';

		if (preg_match('/^\(code\s(3\.11)\)/', $Excerpt['text'], $matches)) {
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
}