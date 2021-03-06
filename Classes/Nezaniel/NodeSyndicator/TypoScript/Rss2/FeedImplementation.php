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
use TYPO3\TYPO3CR\Exception\PageNotFoundException;

/**
 * A TypoScript object implementation to render RSS2 Feeds
 *
 * @Flow\Scope("prototype")
 */
class FeedImplementation extends AbstractRss2Adapter implements Rss2\InlineRenderableFeedInterface {

	/**
	 * @return string
	 */
	public function renderChannel() {
		return $this->tsValue('channel');
	}

	/**
	 * @return string
	 * @throws PageNotFoundException
	 */
	public function evaluate() {
		return $this->renderer->renderFeed($this);
	}

}
