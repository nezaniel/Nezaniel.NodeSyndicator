<?php
namespace Nezaniel\NodeSyndicator\TypoScript;

/*                                                                          *
 * This script belongs to the TYPO3 Flow package "Nezaniel.NodeSyndicator". *
 *                                                                          *
 * It is free software; you can redistribute it and/or modify it under      *
 * the terms of the GNU General Public License, either version 3 of the     *
 * License, or (at your option) any later version.                          *
 *                                                                          *
 * The TYPO3 project - inspiring people to share!                           *
 *                                                                          */
use TYPO3\Flow\Annotations as Flow;
use TYPO3\TypoScript\TypoScriptObjects\AbstractCollectionImplementation;

/**
 * Syndication implementation to render ContentCollections.
 */
class ContentCollectionImplementation extends \TYPO3\Neos\TypoScript\ContentCollectionImplementation {

	/**
	 * Bypass \TYPO3\Neos\TypoScript\ContentCollectionImplementation's evaluation to prevent rendering of wrapping tags in Syndication context
	 *
	 * @return string
	 */
	public function evaluate() {
		return AbstractCollectionImplementation::evaluate();
	}

}