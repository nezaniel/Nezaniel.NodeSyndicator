<?php
namespace Nezaniel\NodeSyndicator\Dto\Atom;

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
use Nezaniel\Syndicator\Dto\Atom as Atom;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;

/**
 * An abstract facade for handling Nodes as syndication DTOs
 */
class AbstractFacade extends \Nezaniel\NodeSyndicator\Dto\AbstractFacade {

	/**
	 * @param $propertyName
	 * @return Atom\Text|NULL
	 */
	protected function extractText($propertyName) {
		$value = $this->nodeDataExtractor->extractMappedProperty($this->getNode(), Syndicator::FORMAT_ATOM, 'feed', $propertyName);
		if ($value !== NULL) {
			return new Atom\Text(
				Atom\TextInterface::TYPE_TEXT,
				$value
			);
		}
		return NULL;
	}

	/**
	 * @param string $propertyName
	 * @param string $expectedChildType
	 * @return \SplObjectStorage
	 */
	protected function extractChildObjectStorage($propertyName, $expectedChildType) {
		$childObjectConfiguration = $this->getNode()->getNodeType()->getConfiguration('syndication.' . Syndicator::FORMAT_ATOM . '.feed.' . $propertyName);
		$mappedChildren = $this->nodeDataExtractor->extractMappedProperty($this->getNode(), Syndicator::FORMAT_ATOM, 'feed', $propertyName);

		$children = new \SplObjectStorage();

		switch ($childObjectConfiguration) {
			case self::CONFIGURATIONMODE_ENTITY:
				if ($this->isChildOfExpectedType($children, $expectedChildType)) {
					$children->attach($mappedChildren);
				}
			break;
			case self::CONFIGURATIONMODE_ENTITYCOLLECTION:
				if (is_array($mappedChildren) || $mappedChildren instanceof \Traversable) {
					foreach ($mappedChildren as $child) {
						if ($this->isChildOfExpectedType($child, $expectedChildType)) {
							$children->attach($mappedChildren);
						}
					}
				}
			break;
			case self::CONFIGURATIONMODE_NODE:
				if ($mappedChildren instanceof NodeInterface) {
					$facade = $this->facadeFactory->create($mappedChildren, Syndicator::FORMAT_ATOM, $expectedChildType);
					if ($facade instanceof \Nezaniel\NodeSyndicator\Dto\AbstractFacade) {
						$children->attach($facade);
					}
				}
			break;
			case self::CONFIGURATIONMODE_NODECOLLECTION:
				if (is_array($mappedChildren) || $mappedChildren instanceof \Traversable) {
					foreach ($mappedChildren as $child) {
						$facade = $this->facadeFactory->create($child, Syndicator::FORMAT_ATOM, $expectedChildType);
						if ($facade instanceof \Nezaniel\NodeSyndicator\Dto\AbstractFacade) {
							$children->attach($facade);
						}
					}
				}
			break;
		}

		return $children;
	}

	/**
	 * @param mixed $child
	 * @param string $expectedType
	 * @return boolean
	 */
	protected function isChildOfExpectedType($child, $expectedType) {
		return ($child instanceof Atom\PersonInterface && $expectedType === 'person'
			|| $child instanceof Atom\CategoryInterface && $expectedType === 'category'
			|| $child instanceof Atom\EntryInterface && $expectedType === 'entry');
	}

}