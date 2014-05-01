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
 * A service for extracting node properties mapped to feed elements via its NodeType configuration
 *
 * @Flow\Scope("singleton")
 */
class MappedNodePropertyExtractor {

	/**
	 * @var NodeInterface
	 */
	protected $node;

	/**
	 * @var array
	 */
	protected $propertyMappingConfiguration = array();


	/**
	 * @return NodeInterface
	 */
	protected function getNode() {
		return $this->node;
	}

	/**
	 * @return array
	 */
	protected function getPropertyMappingConfiguration() {
		return $this->propertyMappingConfiguration;
	}


	/**
	 * @return $this
	 */
	public function reset() {
		unset($this->propertyMappingConfiguration, $this->node);
		return $this;
	}

	/**
	 * @param NodeInterface $node
	 * @param string        $syndicationFormat
	 * @param string        $elementType
	 * @return $this
	 */
	public function initialize(NodeInterface $node, $syndicationFormat, $elementType) {
		$this->node = $node;
		$this->propertyMappingConfiguration = $node->getNodeType()->getConfiguration('syndication.' . $syndicationFormat . '.' . $elementType . '.propertyMapping');
		return $this;
	}


	/**
	 * @param $propertyName
	 * @return mixed|NULL
	 */
	public function extractMappedProperty($propertyName) {
		if (($mappedProperty = $this->getMappedNodeProperty($propertyName)) !== NULL) {
			if ($this->getNode()->hasProperty($mappedProperty)) {
				return $this->getNode()->getProperty($mappedProperty);
			}
		}
		return NULL;
	}


	/**
	 * @param $propertyName
	 * @return string|NULL
	 */
	protected function getMappedNodeProperty($propertyName) {
		if (isset($this->getPropertyMappingConfiguration()[$propertyName])) {
			return ($this->getPropertyMappingConfiguration()[$propertyName]);
		}
		return NULL;
	}

}