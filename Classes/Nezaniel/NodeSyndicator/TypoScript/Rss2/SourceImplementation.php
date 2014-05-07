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
use Nezaniel\Syndicator\Core\Syndicator;
use Nezaniel\Syndicator\Dto\Rss2 as Rss2;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\TypoScript\TypoScriptObjects\AbstractTypoScriptObject;

/**
 * A TypoScript object implementation to render RSS2 Source constructs
 *
 * @Flow\Scope("prototype")
 */
class SourceImplementation extends AbstractRss2Adapter {

	/**
	 * @return string
	 */
	public function evaluate() {
		$source = new Rss2\Source(
			AbstractTypoScriptObject::tsValue('title'),
			$this->renderNodeUri(Syndicator::FORMAT_RSS2)
		);
		return $source->xmlSerialize();
	}

}