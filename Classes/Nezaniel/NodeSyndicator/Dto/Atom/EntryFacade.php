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

class EntryFacade extends AbstractFacade implements Atom\EntryInterface {

	/**
	 * @return \Traversable<Person>
	 */
	public function getAuthors() {
		return $this->extractChildObjectStorage('authors', 'person');
	}

	/**
	 * @return \Traversable<Category>
	 */
	public function getCategories() {
		return $this->extractChildObjectStorage('categories', 'category');
	}

	/**
	 * @return Atom\Content
	 */
	public function getContent() {
		// TODO: Implement getContent() method.
	}

	/**
	 * @return \Traversable<Person>
	 */
	public function getContributors() {
		return $this->extractChildObjectStorage('contributors', 'person');
	}

	/**
	 * @return string
	 */
	public function getId() {
		// TODO: Implement getId() method.
	}

	/**
	 * @return \Traversable<Link>
	 */
	public function getLinks() {
		return $this->extractChildObjectStorage('links', 'link');
	}

	/**
	 * @return \DateTime
	 */
	public function getPublished() {
		return $this->nodeDataExtractor->extractMappedProperty($this->getNode(), 'published', Syndicator::FORMAT_ATOM, 'entry');
	}

	/**
	 * @return Atom\Text
	 */
	public function getRights() {
		return $this->extractText('rights');
	}

	/**
	 * @return Atom\Feed
	 */
	public function getSource() {
		// TODO: Implement getSource() method.
	}

	/**
	 * @return Atom\Text
	 */
	public function getSummary() {
		// TODO: Implement getSummary() method.
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