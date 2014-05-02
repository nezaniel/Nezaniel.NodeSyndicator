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
use Nezaniel\NodeSyndicator\Service\MappedNodePropertyExtractor;
use Nezaniel\NodeSyndicator\Service\NodeService;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Mvc\Routing\UriBuilder;
use TYPO3\Flow\Resource\Publishing\ResourcePublisher;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;

/**
 * An abstract translator for nodes
 */
abstract class AbstractNodeToFeedTranslator implements NodeToFeedTranslatorInterface {

	const IDMODE_IDENTIFIER = 'identifier';
	const IDMODE_URL = 'url';

	/**
	 * @Flow\Inject
	 * @var MappedNodePropertyExtractor
	 */
	protected $mappedNodePropertyExtractor;

	/**
	 * @Flow\Inject
	 * @var NodeService
	 */
	protected $nodeService;

	/**
	 * @Flow\Inject
	 * @var ResourcePublisher
	 */
	protected $resourcePublisher;


	/**
	 * @var UriBuilder
	 */
	protected $uriBuilder;



	/**
	 * @param NodeInterface $node
	 * @param string        $syndicationFormat
	 * @param string        $type
	 * @return string
	 */
	public function translateNodeToId(NodeInterface $node, $syndicationFormat, $type) {
		$idMode = $node->getNodeType()->getConfiguration('syndication.' . $syndicationFormat . '.' . $type . '.idMode');
		switch ($idMode) {
			case self::IDMODE_IDENTIFIER:
				return $node->getIdentifier();
			case self::IDMODE_URL:
				return $this->uriBuilder->reset()->setCreateAbsoluteUri(TRUE)->uriFor('show', array('node' => $node), 'Frontend\Node', 'TYPO3.Neos');
			default:
				return $this->uriBuilder->reset()->setCreateAbsoluteUri(TRUE)->uriFor('show', array('node' => $node), 'Frontend\Node', 'TYPO3.Neos');
		}
	}

	/**
	 * @param NodeInterface $node
	 * @param string $syndicationFormat
	 * @return string
	 * @todo Find out how to use UriBuilder's setFormat without throwing an exception
	 */
	protected function getSyndicationUri(NodeInterface $node, $syndicationFormat) {
		$rssNodeUri = $this->uriBuilder->reset()->setCreateAbsoluteUri(TRUE)->uriFor('show', array('node' => $node), 'Frontend\Node', 'TYPO3.Neos');
		$rssNodeUri = substr($rssNodeUri, 0, strrpos($rssNodeUri, '.')+1) . $syndicationFormat;
		return $rssNodeUri;
	}

	/**
	 * @param string $propertyName
	 * @return mixed|NULL
	 */
	protected function getMappedProperty($propertyName) {
		return $this->mappedNodePropertyExtractor->extractMappedProperty($propertyName);
	}

}