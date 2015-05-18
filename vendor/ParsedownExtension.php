<?php

class Parsedown_Extension extends Parsedown
{
	public function __construct()
	{


		$this->BlockTypes['('] [] = 'Snippet';
		$this->BlockTypes['/'] [] = 'Todo';
		array_unshift($this->BlockTypes['>'], 'SpecialQuote');

	}

	protected function blockSpecialQuote($Line)
	{
		$specialQuotes = array(
			'tip' 			=> ['(?:CIEKAWOSTKA)', 		'Ciekawostka'],
			'warning' 		=> ['(?:UWAGA)', 			'Uwaga!'],
			'question' 		=> ['(?:POMY(?:Ś|S)L)', 	'Pomyśl'],
			'homework' 		=> ['(?:ZADANIE)', 			'Zadanie'],
		);

		foreach($specialQuotes as $className=>$params) {

			list($regExp, $label) = $params;

			if (preg_match('/^>[ ]?'.$regExp.':[ ]?(.*)/i', $Line['text'], $matches)) {
				$Block = array(
					'element' => array(
						'name' => 'blockquote',
						'handler' => 'lines',
						'identified' => true,
						'text' => array(
							$matches[1],
						),
						'attributes' => array(
							'class' => 'box '.$className,
							'data-label' => $label
						)
					),
				);

				return $Block;
			}
		}
	}

	protected function blockSpecialQuoteContinue($Line, array $Block) {
		return parent::blockQuoteContinue($Line, $Block);
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