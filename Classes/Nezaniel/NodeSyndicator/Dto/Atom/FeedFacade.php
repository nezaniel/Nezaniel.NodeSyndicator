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
use Nezaniel\NodeSyndicator\Package;
use Nezaniel\Syndicator\Core\Syndicator;
use Nezaniel\Syndicator\Dto\Atom as Atom;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Media\Domain\Model\ImageVariant;

/**
 * The facade for handling Nodes as Atom\FeedInterfaces
 */
class FeedFacade extends AbstractFacade implements Atom\FeedInterface {

	/**
	 * @return \SplObjectStorage<PersonInterface>
	 */
	public function getAuthors() {
		return $this->extractChildObjectStorage('authors', 'person');
	}

	/**
	 * @return \SplObjectStorage<CategoryInterface>
	 */
	public function getCategories() {
		return $this->extractChildObjectStorage('categories', 'category');
	}

	/**
	 * @return \SplObjectStorage<PersonInterface>
	 */
	public function getContributors() {
		return $this->extractChildObjectStorage('contributors', 'person');
	}

	/**
	 * @return \SplObjectStorage<EntryInterface>
	 */
	public function getEntries() {
		return $this->extractChildObjectStorage('entries', 'entry');
	}

	/**
	 * @return Atom\Generator
	 */
	public function getGenerator() {
		return new Atom\Generator(
			'Nezaniel.NodeSyndicator',
			'https://github.com/nezaniel/Nezaniel.NodeSyndicator',
			Package::VERSION
		);
	}

	/**
	 * @return string
	 */
	public function getIcon() {
		$icon = $this->nodeDataExtractor->extractMappedProperty($this->getNode(), Syndicator::FORMAT_ATOM, 'feed', 'icon');
		if ($icon instanceof ImageVariant) {
			return $icon->getResource()->getUri();
		} elseif (is_string($icon)) {
			return $icon;
		}
		return NULL;
	}

	/**
	 * @return string
	 */
	public function getId() {
		return $this->nodeDataExtractor->extractIdentifier($this->getNode(), Syndicator::FORMAT_ATOM, 'feed');
	}

	/**
	 * @return \SplObjectStorage<LinkInterface>
	 */
	public function getLinks() {
		return $this->extractChildObjectStorage('links', 'link');
	}

	/**
	 * @return string
	 */
	public function getLogo() {
		$logo = $this->nodeDataExtractor->extractMappedProperty($this->getNode(), Syndicator::FORMAT_ATOM, 'feed', 'logo');
		if ($logo instanceof ImageVariant) {
			return $logo->getResource()->getUri();
		} elseif (is_string($logo)) {
			return $logo;
		}
		return NULL;
	}

	/**
	 * @return Atom\Text
	 */
	public function getRights() {
		return $this->extractText('rights');
	}

	/**
	 * @return Atom\Text
	 */
	public function getSubtitle() {
		return $this->extractText('subtitle');
	}

	/**
	 * @return Atom\Text
	 */
	public function getTitle() {
		return $this->extractText('title');
	}

	/**
	 * @return \DateTime
	 * @todo Use TYPO.TYPO3CR Node lastChanged, once it's implemented
	 */
	public function getUpdated() {
		return new \DateTime();
	}

}