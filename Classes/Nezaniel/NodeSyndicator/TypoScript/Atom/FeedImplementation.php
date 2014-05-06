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
use Nezaniel\NodeSyndicator\Package;
use Nezaniel\Syndicator\Core\Syndicator;
use Nezaniel\Syndicator\Dto\Atom as Atom;
use Nezaniel\Syndicator\View\AtomInlineRenderer;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\TYPO3CR\Exception\PageNotFoundException;

/**
 * A TypoScript object implementation to render
 *
 * @Flow\Scope("prototype")
 * @todo check whether we can just extend Feed and use xmlSerialize()
 */
class FeedImplementation extends AbstractAtomAdapter implements Atom\InlineRenderableFeedInterface {

	/**
	 * @var string
	 */
	protected $feedMode = AtomInlineRenderer::FEEDMODE_FEED;


	/**
	 * @return string
	 */
	public function getId() {
		return $this->renderNodeIdentifier(Syndicator::FORMAT_ATOM);
	}

	/**
	 * @return string
	 */
	public function renderTitle() {
		return $this->renderText('title', 'title');
	}

	/**
	 * @return string
	 */
	public function renderAuthors() {
		return $this->tsValue('authors');
	}

	/**
	 * @return string
	 * @todo auto-handle locales, once Neos completely supports them
	 */
	public function renderLinks() {
		$selfLink = new Atom\Link(
			$this->renderNodeUri(Syndicator::FORMAT_ATOM),
			Atom\LinkInterface::REL_SELF
		);
		$links = $selfLink->xmlSerialize();
		$links .= $this->tsValue('links');

		return $links;
	}

	/**
	 * @return string
	 */
	public function renderCategories() {
		return $this->tsValue('categories');
	}

	/**
	 * @return string
	 */
	public function renderContributors() {
		return $this->tsValue('contributors');
	}

	/**
	 * @return string
	 */
	public function renderGenerator() {
		$renderedTsGenerator = $this->tsValue('generator');
		if ($renderedTsGenerator === '' || $renderedTsGenerator === NULL) {
			$generator = new Atom\Generator(
				'Nezaniel.NodeSyndicator powered by TYPO3.Neos',
				'https://github.com/nezaniel/Nezaniel.NodeSyndicator',
				Package::VERSION
			);
			return $generator->xmlSerialize();
		}
		return $renderedTsGenerator;
	}

	/**
	 * @return string
	 */
	public function renderRights() {
		return $this->renderText('rights');
	}

	/**
	 * @return string
	 */
	public function renderSubtitle() {
		return $this->renderText('subtitle', 'title');
	}

	/**
	 * @return string
	 */
	public function renderEntries() {
		return $this->tsValue('entries');
	}

	/**
	 * @return string
	 */
	public function getIcon() {
		return $this->renderImage('icon');
	}

	/**
	 * @return string
	 */
	public function getLogo() {
		return $this->renderImage('logo');
	}

	/**
	 * @return \DateTime
	 * @todo return something useful, once TYPO3.TYPO3CR supports lastChanged for nodes
	 */
	public function getUpdated() {
		$updated = $this->tsValue('updated');
		if (!$updated instanceof \DateTime) {
			return new \DateTime();
		}
		return $updated;
	}


	/**
	 * Render the properties defined via TypoScript as an Atom Feed
	 *
	 * @return string
	 * @throws PageNotFoundException
	 */
	public function evaluate() {
		if ($this->getNode()->getNodeType()->isOfType('Nezaniel.NodeSyndicator:Syndication')
			&& $this->getNode()->getProperty('feedAsAtom')) {
				//header('Content-Type:' . Syndicator::CONTENTTYPE_ATOM);
				return $this->renderer->renderFeed($this, $this->getFeedMode());
		}
		throw new PageNotFoundException('The requested node is not configured as an Atom feed.', 1399133728);
	}

	/**
	 * @return string
	 */
	protected function getFeedMode() {
		return $this->feedMode;
	}

}