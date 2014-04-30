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
 * This service provides methods for advanced node handling
 *
 * @Flow\Scope("singleton")
 */
class NodeService {

	const DESCRIPTIONMODE_PRIMARYCONTENT = 'primaryContent';

	const DESCRIPTIONMODE_PATH = 'path';

	const DESCRIPTIONMODE_PROPERTY = 'property';


	/**
	 * @param NodeInterface $parentNode
	 * @param string $itemFilter
	 * @param boolean $recursive
	 * @return array
	 */
	public function getItemNodes(NodeInterface $parentNode, $itemFilter, $recursive = FALSE) {
		$itemNodes = $parentNode->getChildNodes($itemFilter);

		if ($recursive && sizeof($itemNodes) > 0) {
			foreach ($itemNodes as $itemNode) {
				$itemNodes = array_merge($itemNodes, $this->getItemNodes($itemNode, $itemFilter, $recursive));
			}
		}

		return $itemNodes;
	}

	/**
	 * @param NodeInterface $node
	 * @param $syndicationFormat
	 * @param $mode
	 * @return string|NULL
	 */
	public function extractDescription(NodeInterface $node, $syndicationFormat, $mode) {
		$syndicationConfiguration = $node->getNodeType()->getConfiguration('syndication.' . $syndicationFormat . '.' . $mode . '.propertyMapping');
		if ($syndicationConfiguration === NULL || !isset($syndicationConfiguration['descriptionMode'])) {
			return NULL;
		}
		switch ($syndicationConfiguration['descriptionMode']) {
			case self::DESCRIPTIONMODE_PROPERTY:
				if (isset($syndicationConfiguration['description'])	&& $node->hasProperty($syndicationConfiguration['description'])) {
					return $node->getProperty($syndicationConfiguration['description']);
				}
				return NULL;
			case self::DESCRIPTIONMODE_PRIMARYCONTENT:
				return $this->collapseTextNodes($node->getPrimaryChildNode());
			break;
			case self::DESCRIPTIONMODE_PATH:
				if (isset($syndicationConfiguration['descriptionNodePath'])
					&& ($descriptionNode = $node->getNode($syndicationConfiguration['descriptionNodePath'])) instanceof NodeInterface) {
						return $this->collapseTextNodes($descriptionNode);
				}
				return NULL;
		}
	}

	/**
	 * @param NodeInterface $parentNode
	 * @return string
	 */
	public function collapseTextNodes(NodeInterface $parentNode) {
		$description = '';
		$descriptionContentNodes = $parentNode->getChildNodes('TYPO3.Neos.NodeTypes:Text');
		if (sizeof($descriptionContentNodes) > 0) {
			foreach ($descriptionContentNodes as $i => $descriptionContentNode) {
				/** @var NodeInterface $descriptionContentNode */
				$description .= $descriptionContentNode->getProperty('text');

				if ($i < sizeof($descriptionContentNodes)-1) {
					$description .= "\n";
				}
			}
		}
		return $description;
	}

}