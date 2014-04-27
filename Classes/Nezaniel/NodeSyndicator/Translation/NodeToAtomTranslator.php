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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Nezaniel\Syndicator\Dto\Atom\Feed;
use Nezaniel\Syndicator\Dto\Atom\Generator;
use Nezaniel\Syndicator\Dto\Atom\Link;
use Nezaniel\Syndicator\Dto\Atom\Person;
use Nezaniel\Syndicator\Dto\Atom\Text;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;

/**
 * The translator capable of converting Nodes to Atom Feeds
 *
 * @Flow\Scope("singleton")
 */
class NodeToAtomTranslator {

	public function translateNodeToFeed(NodeInterface $node) {
		$atomFeed = new Feed(
			$node->getIdentifier(),
			new Text('text', $node->getName()),
			new \DateTime(),
			//$node->getLastChanged(),
			$this->translateNodeToPersonCollection($node, 'authors', 'author'),
			$this->translateNodeToLink($node),
			NULL,
			NULL,
			new Generator('Feeder powered by TYPO3.Neos', 'https://github.com/nezaniel/Nezaniel.Feeder', '0.1.0dev'),
			NULL,
			NULL,
			NULL,
			NULL,
			array(
				'content' => 'http://purl.org/rss/1.0/modules/content/'
			)
		);
		return $atomFeed;
	}


	/**
	 * Translates the given Node to a person collection.
	 *
	 * @param NodeInterface $node
	 * @param string $collectionName
	 * @param string $singleName
	 * @return ArrayCollection
	 */
	public function translateNodeToPersonCollection(NodeInterface $node, $collectionName, $singleName) {
		$persons = new ArrayCollection();

		$unmappedPersons = $this->getMappedPropertyValue($node, $collectionName);
		if ($unmappedPersons instanceof Collection) {
			foreach ($unmappedPersons as $unmappedPersonNode) {
				if ($this->getAvailableMappedPropertyName($node, 'name') !== FALSE) {
					$persons->add($this->translateNodeToPerson($unmappedPersonNode));
				}
			}
		} elseif ($this->getAvailableMappedPropertyName($node, $singleName . '.name') !== FALSE) {
			$persons->add($this->translateNodeToPerson($node, $singleName));
		}

		return $persons;
	}

	/**
	 * Translates the given Node to a single person.
	 *
	 * Does not check whether the given mapping configuration is valid.
	 *
	 * @param NodeInterface $node
	 * @param string $prefix
	 * @return Person
	 */
	public function translateNodeToPerson(NodeInterface $node, $prefix = '') {
		return new Person(
			$this->getMappedPropertyValue($node, ($prefix?$prefix . '.':'') . 'name'),
			$this->getMappedPropertyValue($node, ($prefix?$prefix . '.':'') . 'uri'),
			$this->getMappedPropertyValue($node, ($prefix?$prefix . '.':'') . 'email')
		);
	}

	/**
	 * @param NodeInterface $node
	 * @return Link
	 * @todo determine fallback type and language. what about title?
	 */
	public function translateNodeToLink(NodeInterface $node) {
		// mapping first

		// fallback to node itself
		return new Link($node->getPath(), Link::REL_ALTERNATE, 'text/html', 'de-de', $node->getProperty('title'));
	}

	/**
	 * @param NodeInterface $node
	 * @param string $propertyName
	 * @return mixed|NULL
	 */
	protected function getMappedPropertyValue(NodeInterface $node, $propertyName) {
		$availableMappedPropertyName = $this->getAvailableMappedPropertyName($node, $propertyName);
		if ($availableMappedPropertyName !== FALSE) {
			if (strpos($availableMappedPropertyName, '_') === 0) {
				return $node->{'get' . ucfirst(substr($availableMappedPropertyName, 1))}();
			}
			return $node->getProperty($availableMappedPropertyName);
		}
		return NULL;
	}

	/**
	 * @param NodeInterface $node
	 * @param string $propertyName
	 * @return string|FALSE
	 */
	protected function getAvailableMappedPropertyName(NodeInterface $node, $propertyName) {
		$nodeTypeConfiguration = $node->getNodeType()->getConfiguration('.');
		if (!isset($nodeTypeConfiguration['feederMapping'][$propertyName])) {
			return FALSE;
		}

		if (strpos($nodeTypeConfiguration['feederMapping'][$propertyName], '_') === 0) {
			if (method_exists($node, 'get' . ucfirst(substr($nodeTypeConfiguration['feederMapping'][$propertyName], 1)))) {
				return $nodeTypeConfiguration['feederMapping'][$propertyName];
			} else {
				return FALSE;
			}
		} else {
			if ($node->hasProperty($nodeTypeConfiguration['feederMapping'][$propertyName])) {
				return $nodeTypeConfiguration['feederMapping'][$propertyName];
			} else {
				return FALSE;
			}
		}
	}

}