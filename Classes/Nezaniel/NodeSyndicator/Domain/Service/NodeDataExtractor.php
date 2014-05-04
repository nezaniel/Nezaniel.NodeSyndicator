<?php
namespace Nezaniel\NodeSyndicator\Domain\Service;

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
use TYPO3\Flow\Http\Request;
use TYPO3\Flow\Mvc\ActionRequest;
use TYPO3\Flow\Mvc\Routing\UriBuilder;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;

/**
 * This service provides methods for advanced node handling
 *
 * @Flow\Scope("singleton")
 */
class NodeDataExtractor {

	/**
	 * The description will be extracted from the Node's primary content's text nodes
	 */
	const DESCRIPTIONMODE_PRIMARYCONTENT = 'primaryContent';

	/**
	 * The description will be extracted from a child Node's text nodes.
	 * The child Node will be determined via the descriptionNodePath configuration
	 */
	const DESCRIPTIONMODE_PATH = 'path';

	/**
	 * The description will be extracted from the Node property mapped to description
	 */
	const DESCRIPTIONMODE_PROPERTY = 'property';


	/**
	 * The Node's id will be determined by its URL
	 */
	const IDMODE_URL = 'url';

	/**
	 * The Node's id will be determined by its uuid
	 */
	const IDMODE_UUID = 'uuid';


	/**
	 * @Flow\Inject
	 * @var UriBuilder
	 */
	protected $uriBuilder;


	/**
	 * @return void
	 */
	public function initializeObject() {
		$this->uriBuilder->setRequest(new ActionRequest(new Request($_GET, $_POST, $_FILES, $_SERVER)));
	}


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
		$descriptionMode = $node->getNodeType()->getConfiguration('syndication.' . $syndicationFormat . '.' . $mode . '.descriptionMode');
		if ($descriptionMode === NULL) {
			return NULL;
		}
		switch ($descriptionMode) {
			case self::DESCRIPTIONMODE_PROPERTY:
				return $this->extractMappedProperty($node, 'description', $syndicationFormat, $mode);
			case self::DESCRIPTIONMODE_PRIMARYCONTENT:
				return $this->collapseTextNodes($node->getPrimaryChildNode());
			break;
			case self::DESCRIPTIONMODE_PATH:
				$descriptionNodePath = $descriptionMode = $node->getNodeType()->getConfiguration('syndication.' . $syndicationFormat . '.' . $mode . '.descriptionNodePath');
				if ($descriptionNodePath !== NULL
					&& ($descriptionNode = $node->getNode($descriptionNodePath)) instanceof NodeInterface) {
						return $this->collapseTextNodes($descriptionNode);
				}
				return NULL;
			default:
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

	/**
	 * @param NodeInterface $node
	 * @param string        $propertyName
	 * @param string        $syndicationFormat
	 * @param string        $mode
	 * @return mixed|NULL
	 */
	public function extractMappedProperty(NodeInterface $node, $propertyName, $syndicationFormat, $mode) {
		if (($mappedProperty = $node->getNodeType()->getConfiguration('syndication.' . $syndicationFormat . '.' . $mode . '.propertyMapping.' . $propertyName)) !== NULL) {
			if ($node->hasProperty($mappedProperty)) {
				return $node->getProperty($mappedProperty);
			}
		}
		return NULL;
	}

	/**
	 * @param NodeInterface $node
	 * @param string        $syndicationFormat
	 * @param string        $mode
	 * @return string
	 */
	public function extractIdentifier(NodeInterface $node, $syndicationFormat, $mode) {
		$idMode = $node->getNodeType()->getConfiguration('syndication.' . $syndicationFormat . '.' . $mode . '.idMode');
		switch($idMode) {
			case self::IDMODE_UUID:
				return 'urn:uuid:' . $node->getIdentifier();
			case self::IDMODE_URL:
				return $this->uriBuilder
					->reset()
					->setCreateAbsoluteUri(TRUE)
					->setFormat($syndicationFormat)
					->uriFor('syndicate', array('node' => $node), 'Node', 'Nezaniel.NodeSyndicator');
			default:
				return 'urn:uuid:' . $node->getIdentifier();
		}
	}

}