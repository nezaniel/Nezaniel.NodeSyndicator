<?php
namespace Nezaniel\NodeSyndicator\TypoScript\Rss2;

/*                                                                          *
 * This script belongs to the TYPO3 Flow package "Nezaniel.NodeSyndicator". *
 *                                                                          *
 * It is free software; you can redistribute it and/or modify it under      *
 * the terms of the GNU General Public License, either version 3 of the     *
 * License, or (at your option) any later version.                          *
 *                                                                          *
 * The TYPO3 project - inspiring people to share!                           *
 *                                                                          */
use Nezaniel\Syndicator\Dto\Rss2 as Rss2;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Media\Domain\Model\AssetInterface;

/**
 * A TypoScript object implementation to render RSS2 Items
 *
 * @Flow\Scope("prototype")
 */
class ItemImplementation extends AbstractRss2Adapter implements Rss2\InlineRenderableItemInterface {

	/**
	 * @return string
	 */
	public function getAuthor() {
		return $this->tsValue('author');
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
	public function getComments() {
		return $this->tsValue('comments');
	}

	/**
	 * @return string
	 */
	public function getDescription() {
		return $this->tsValue('description');
	}

	/**
	 * @return string
	 */
	public function renderEnclosure() {
		$enclosureSource = $this->tsValue('enclosure');
		if ($enclosureSource instanceof AssetInterface) {
			$enclosure = new Rss2\Enclosure(
				$this->resourcePublisher->getPersistentResourceWebUri($enclosureSource->getResource()),
				filesize($enclosureSource->getResource()->getUri()),
				$enclosureSource->getResource()->getMediaType()
			);
			return $enclosure->xmlSerialize();
		}
		return '';
	}

	/**
	 * @return string
	 */
	public function getGuid() {
		return $this->renderNodeIdentifier('html');
	}

	/**
	 * @return boolean
	 */
	public function getPermaLink() {
		return ($this->tsValue('idMode') === self::IDMODE_UUID);
	}

	/**
	 * @return string
	 */
	public function getLink() {
		$link = $this->tsValue('link');
		if ($link === NULL) {
			return $this->renderNodeUri('html');
		}
		return $link;
	}

	/**
	 * @return \DateTime
	 */
	public function getPubDate() {
		return $this->tsValue('pubDate');
	}

	/**
	 * @return string
	 */
	public function renderSource() {
		return $this->tsValue('source');
	}

	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->tsValue('title');
	}


	/**
	 * @return string
	 */
	public function evaluate() {
		return $this->renderer->renderItem($this);
	}

}