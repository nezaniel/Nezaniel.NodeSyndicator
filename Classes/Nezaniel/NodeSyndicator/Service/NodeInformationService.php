<?php
namespace Nezaniel\NodeSyndicator\Service;

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
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;

/**
 * This service provides helpful information about nodes when it comes to syndication
 *
 * @Flow\Scope("singleton")
 */
class NodeInformationService {

	/**
	 * Returns whether the given Node is correctly configured for syndication in the given format
	 *
	 * @param NodeInterface $node
	 * @param string $syndicationFormat
	 * @return boolean
	 */
	public function canNodeBeSyndicated(NodeInterface $node, $syndicationFormat) {
		return ($node->getNodeType()->isOfType('Nezaniel.NodeSyndicator:Syndication')
			&& $node->getNodeType()->getConfiguration('syndication.' . $syndicationFormat . '.feed') !== NULL
		);
	}

	/**
	 * Returns whether the given Node is configured to be syndicated
	 *
	 * @param NodeInterface $node
	 * @param string $syndicationFormat
	 * @return boolean
	 */
	public function isNodeToBeSyndicated(NodeInterface $node, $syndicationFormat) {
		return $node->getProperty('feedAs' . ucfirst($syndicationFormat));
	}



}