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
use Nezaniel\Syndicator\Core\Syndicator;
use Nezaniel\Syndicator\Dto\Rss2 as Rss2;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Utility\Arrays;
use TYPO3\Media\Domain\Model\ImageVariant;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;

/**
 * A TypoScript object implementation to render RSS2 Feeds
 *
 * @Flow\Scope("prototype")
 */
class ChannelImplementation extends AbstractRss2Adapter implements Rss2\InlineRenderableChannelInterface {

	/**
	 * @return string
	 */
	public function renderCategories() {
		return $this->tsValue('categories');
	}

	/**
	 * @return string
	 */
	public function renderCloud() {
		return $this->tsValue('cloud');
	}

	/**
	 * @return string
	 */
	public function getCopyright() {
		return $this->tsValue('copyright');
	}

	/**
	 * @return string
	 */
	public function getDescription() {
		return strip_tags($this->tsValue('description'));
	}

	/**
	 * @return string
	 */
	public function getDocs() {
		return $this->tsValue('docs');
	}

	/**
	 * @return string
	 */
	public function getGenerator() {
		$generator = $this->tsValue('generator');
		if ($generator === NULL) {
			$generator = 'Nezaniel.NodeSyndicator powered by TYPO3.Neos';
		}
		return $generator;
	}

	/**
	 * @return string
	 */
	public function renderImage() {
		$value = $this->tsValue('image');
		$description = '';
		if (is_string($value)) {
			return $value;
		}
		if ($value instanceof NodeInterface && $value->getNodeType()->isOfType('TYPO3.Neos.NodeTypes:Image')) {
			if ($value->getProperty('hasCaption')) {
				$description = $value->getProperty('caption');
			}
			$value = $value->getProperty('image');
		}
		if ($value instanceof ImageVariant) {
			$image = new Rss2\Image(
				$this->resourcePublisher->getPersistentResourceWebUri($value->getResource()),
				$this->getTitle(),
				$this->getLink(),
				$value->getWidth(),
				$value->getHeight(),
				$description
			);
			return $image->xmlSerialize();
		}
	}

	/**
	 * @return string
	 */
	public function renderItems() {
		return $this->tsValue('items');
	}

	/**
	 * @return string
	 * @todo automatize this
	 */
	public function getLanguage() {
		return $this->tsValue('language');
	}

	/**
	 * @return \DateTime
	 */
	public function getLastBuildDate() {
		return $this->tsValue('lastBuildDate');
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
	 * @return string
	 */
	public function getManagingEditor() {
		return $this->tsValue('managingEditor');
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
	public function getRating() {
		return $this->tsValue('rating');
	}

	/**
	 * @return array
	 */
	public function getSkipDays() {
		return Arrays::trimExplode(',', $this->tsValue('skipDays'));
	}

	/**
	 * @return array
	 */
	public function getSkipHours() {
		return Arrays::trimExplode(',', $this->tsValue('skipHours'));
	}

	/**
	 * @return string
	 */
	public function renderTextInput() {
		return $this->tsValue('textInput');
	}

	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->tsValue('title');
	}

	/**
	 * @return integer
	 */
	public function getTtl() {
		return $this->tsValue('ttl');
	}

	/**
	 * @return string
	 */
	public function getWebMaster() {
		return $this->tsValue('webMaster');
	}

	/**
	 * @return string
	 */
	public function getAtomLinkUrl() {
		$atomLinkUrl = $this->tsValue('atomLinkUrl');
		if ($atomLinkUrl === NULL) {
			$atomLinkUrl = $this->renderNodeUri(Syndicator::FORMAT_RSS2);
		}
		return $atomLinkUrl;
	}


	/**
	 * @return string
	 */
	public function evaluate() {
		return $this->renderer->renderChannel($this);
	}

}