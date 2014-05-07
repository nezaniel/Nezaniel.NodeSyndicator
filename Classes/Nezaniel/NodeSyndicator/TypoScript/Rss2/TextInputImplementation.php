<?php
namespace Nezaniel\NodeSyndicator\TypoScript\Rss2;

/*                                                                          *
 * This script belongs to the TYPO3 Flow package "Nezaniel.NodeSyndicator". *
 *                                                                          *
 * It is free software; you can redistribute it and/or modify it under      *
 * the terms of the GNU General Public License, either version 3 of the     *
 * License, or (at your option) any later version.                          *
 *                                                                          *
 * The TYPO3 project - inspiring people to share!                           *
 *                                                                          */
use Nezaniel\Syndicator\Dto\Rss2 as Rss2;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\TypoScript\TypoScriptObjects\AbstractTypoScriptObject;

/**
 * A TypoScript object implementation to render RSS2 TextInput constructs
 *
 * @Flow\Scope("prototype")
 */
class TextInputImplementation extends AbstractTypoScriptObject {

	/**
	 * @return string
	 */
	public function evaluate() {
		$textInput = new Rss2\TextInput(
			$this->tsValue('title'),
			$this->tsValue('description'),
			$this->tsValue('name'),
			$this->tsValue('link')
		);
		return $textInput->xmlSerialize();
	}

}