<?php
namespace Nezaniel\NodeSyndicator\TypoScript\Atom;

/*                                                                          *
 * This script belongs to the TYPO3 Flow package "Nezaniel.NodeSyndicator". *
 *                                                                          *
 * It is free software; you can redistribute it and/or modify it under      *
 * the terms of the GNU General Public License, either version 3 of the     *
 * License, or (at your option) any later version.                          *
 *                                                                          *
 * The TYPO3 project - inspiring people to share!                           *
 *                                                                          */
use Nezaniel\Syndicator\Dto\Atom as Atom;
use TYPO3\Flow\Annotations as Flow;

/**
 * A TypoScript object implementation to render
 *
 * @Flow\Scope("prototype")
 */
class EntryImplementation extends AbstractAtomFacade implements Atom\EntryInterface {

	/**
	 * @var \SplObjectStorage<Atom\Person>
	 */
	protected $authors;

	/**
	 * @var \SplObjectStorage<Atom\Category>
	 */
	protected $categories;

	/**
	 * @var Atom\Content;
	 */
	protected $content;


	/**
	 * @return \SplObjectStorage<Atom\Person>
	 */
	public function getAuthors() {
		return $this->authors;
	}

	/**
	 * @param \SplObjectStorage $authors
	 */
	public function setAuthors(\SplObjectStorage $authors) {
		$this->authors = $authors;
	}

	/**
	 * @return \SplObjectStorage<Atom\Category>
	 */
	public function getCategories() {
		return $this->categories;
	}

	/**
	 * @param \SplObjectStorage $categories
	 */
	public function setCategories(\SplObjectStorage $categories) {
		$this->categories = $categories;
	}

	/**
	 * @return Atom\Content
	 */
	public function getContent() {
		return $this->content;
	}

	/**
	 * @param \Nezaniel\Syndicator\Dto\Atom\Content $content
	 */
	public function setContent($content) {
		$this->content = $content;
	}

	/**
	 * @return \Traversable<Person>
	 */
	public function getContributors() {
		// TODO: Implement getContributors() method.
	}

	/**
	 * @return string
	 */
	public function getId() {
		return $this->renderNodeIdentifier('html');
	}

	/**
	 * @return \Traversable<Link>
	 */
	public function getLinks() {
		// TODO: Implement getLinks() method.
	}

	/**
	 * @return \DateTime
	 */
	public function getPublished() {
		// TODO: Implement getPublished() method.
	}

	/**
	 * @return Atom\Text
	 */
	public function getRights() {
		// TODO: Implement getRights() method.
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
		return $this->renderText('summary', 'text');
	}

	/**
	 * @return Atom\Text
	 */
	public function getTitle() {
		return $this->renderText('title', 'title');
	}

	/**
	 * @return \DateTime
	 * @todo return something useful, once TYPO3.TYPO3CR supports lastChanged for nodes
	 */
	public function getUpdated() {
		return new \DateTime();
	}


	/**
	 * @return string
	 */
	public function evaluate() {
		$content = $this->tsValue('content');
		if ($content !== NULL) $this->setContent(new Atom\Content(Atom\Content::TYPE_XHTML, '<div xmlns="http://www.w3.org/1999/xhtml">' .  $content . '</div>'));
		return $this->renderer->renderEntry($this);
	}

}