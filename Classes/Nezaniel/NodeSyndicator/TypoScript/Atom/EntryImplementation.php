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
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;
use TYPO3\TypoScript\TypoScriptObjects\AbstractTypoScriptObject;

/**
 * A TypoScript object implementation to render
 *
 * @Flow\Scope("prototype")
 */
class EntryImplementation extends AbstractAtomAdapter implements Atom\InlineRenderableEntryInterface {

	/**
	 * @return string
	 */
	public function getId() {
		return $this->renderNodeIdentifier('html');
	}

	/**
	 * @return string
	 */
	public function renderTitle() {
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
	public function renderAuthors() {
		return $this->tsValue('authors');
	}

	/**
	 * @return string
	 */
	public function renderContent() {
		$content = new Atom\Content(
			Atom\ContentInterface::TYPE_HTML,
			$this->tsValue('content')
		);
		return $content->xmlSerialize();
	}

	/**
	 * @return string
	 */
	public function renderLinks() {
		$selfLink = new Atom\Link(
			$this->renderNodeUri('html'),
			Atom\LinkInterface::REL_SELF
		);
		$links = $selfLink->xmlSerialize();
		$links .= $this->tsValue('links');

		return $links;
	}

	/**
	 * @return string
	 */
	public function renderSummary() {
		return $this->renderText('summary', 'text');
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
	 * @return \DateTime
	 */
	public function getPublished() {
		return $this->tsValue('published');
	}

	/**
	 * @return string
	 */
	public function renderSource() {
		if ($this->getSourceFeedIdentifier() !== $this->getFeedIdentifier()) {
			return $this->tsValue('source');
		}
		return '';
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
	public function evaluate() {
		return $this->renderer->renderEntry($this);
	}

	/**
	 * @return string
	 */
	protected function getFeedIdentifier() {
		$feedIdentifier = '';
		$currentContext = $this->tsRuntime->popContext();
		$parentContext = $this->tsRuntime->getCurrentContext();
		if (isset($parentContext['node'])) {
			$feedNode = $parentContext['node'];
			if ($feedNode instanceof NodeInterface) {
				$feedIdentifier = $feedNode->getIdentifier();
			}
		}
		$this->tsRuntime->pushContextArray($currentContext);
		return $feedIdentifier;
	}

	/**
	 * @return string
	 */
	protected function getSourceFeedIdentifier() {
		$sourceFeed = AbstractTypoScriptObject::tsValue('sourceFeed');
		if ($sourceFeed instanceof NodeInterface) {
			return $sourceFeed->getIdentifier();
		}
		return '';
	}

}
