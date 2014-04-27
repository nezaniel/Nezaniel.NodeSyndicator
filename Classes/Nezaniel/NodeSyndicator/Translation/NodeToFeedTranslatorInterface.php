<?php
namespace Nezaniel\NodeSyndicator\Translation;

/*                                                                          *
 * This script belongs to the TYPO3 Flow package "Nezaniel.NodeSyndicator". *
 *                                                                          *
 * It is free software; you can redistribute it and/or modify it under      *
 * the terms of the GNU General Public License, either version 3 of the     *
 * License, or (at your option) any later version.                          *
 *                                                                          *
 * The TYPO3 project - inspiring people to share!                           *
 *                                                                          */
use Nezaniel\Syndicator\Dto\AbstractXmlSerializableFeed;
use TYPO3\Flow\Mvc\Routing\UriBuilder;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;

/**
 * The interface for node to feed translators
 */
interface NodeToFeedTranslatorInterface {

	/**
	 * @param NodeInterface $node
	 * @param UriBuilder $uriBuilder
	 * @return AbstractXmlSerializableFeed
	 */
	public function translateNodeToFeed(NodeInterface $node, UriBuilder $uriBuilder);

}