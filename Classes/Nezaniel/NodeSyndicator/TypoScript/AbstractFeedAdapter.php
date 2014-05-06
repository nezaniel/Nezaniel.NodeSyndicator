<?php
namespace Nezaniel\NodeSyndicator\TypoScript;

/*                                                                         *
 * This script belongs to the TYPO3 Flow package "Nezaniel.NodeSyndicator" *
 *                                                                         *
 * It is free software; you can redistribute it and/or modify it under     *
 * the terms of the GNU General Public License, either version 3 of the    *
 * License, or (at your option) any later version.                         *
 *                                                                         *
 * The TYPO3 project - inspiring people to share!                          *
 *                                                                         */
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Mvc\Routing\UriBuilder;
use TYPO3\Flow\Resource\Publishing\ResourcePublisher;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;
use TYPO3\TypoScript\TypoScriptObjects\AbstractArrayTypoScriptObject;

/**
 * An abstract TypoScript object implementation to render syndication feeds
 */
abstract class AbstractFeedAdapter extends AbstractArrayTypoScriptObject {

	const IDMODE_UUID = 'uuid';
	const IDMODE_URL = 'url';


	/**
	 * @Flow\Inject
	 * @var ResourcePublisher
	 */
	protected $resourcePublisher;


	/**
	 * @param $syndicationFormat
	 * @return string|NULL
	 */
	protected function renderNodeIdentifier($syndicationFormat) {
		if ($this->getNode() instanceof NodeInterface) {
			switch($this->tsValue('idMode')) {
				case self::IDMODE_UUID:
					return 'urn:uuid:' . $this->getNode()->getIdentifier();
				case self::IDMODE_URL:
					return $this->renderNodeUri($syndicationFormat);
				default:
					return 'urn:uuid:' . $this->getNode()->getIdentifier();
			}
		}
		return NULL;
	}

	/**
	 * @param string $syndicationFormat
	 * @return string
	 */
	protected function renderNodeUri($syndicationFormat) {
		return $this->getUriBuilder()
			->reset()
			->setCreateAbsoluteUri(TRUE)
			->setFormat($syndicationFormat)
			->uriFor('show', array('node' => $this->getNode()), 'Frontend\Node', 'TYPO3.Neos');
	}

	/**
	 * @return NULL|NodeInterface
	 */
	protected function getNode() {
		return parent::tsValue('node');
	}

	/**
	 * @return UriBuilder
	 */
	protected function getUriBuilder() {
		return $this->tsRuntime->getControllerContext()->getUriBuilder();
	}

	/**
	 * @param string $path
	 * @return mixed
	 */
	protected function tsValue($path) {
		if ($this->getNode() instanceof NodeInterface) {
			$nodeTypeDistinctionPath = str_replace('.', '', $this->getNode()->getNodeType()->getName());
			return parent::tsValue($nodeTypeDistinctionPath . '/' . $path);
		} else {
			return parent::tsValue($path);
		}
	}

}