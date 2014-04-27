<?php
namespace Nezaniel\NodeSyndicator\Translation;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Nezaniel.Feeder".       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */
use Doctrine\Common\Collections\ArrayCollection;
use Nezaniel\Syndicator\Core\Syndicator;
use Nezaniel\Syndicator\Dto\Rss2 as Rss2;
use Nezaniel\NodeSyndicator\Translation\Exception as Exception;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Mvc\Routing\UriBuilder;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;

/**
 * The translator capable of converting Nodes to RSS 2.0 Feeds
 *
 * @Flow\Scope("singleton")
 */
class NodeToRss2Translator extends AbstractNodeToFeedTranslator {

	const CHANNELMODE_PRIMARYCONTENT = 'primaryContent';
	const CHANNELMODE_PATH = 'path';
	const CHANNELMODE_SELF = 'self';

	const ITEMDESCRIPTIONMODE_PRIMARYCONTENT = 'primaryContent';
	const ITEMDESCRIPTIONMODE_PATH = 'path';


	/**
	 * @var \TYPO3\Flow\Mvc\Routing\UriBuilder
	 */
	protected $uriBuilder;


	/**
	 * @param NodeInterface $feedNode
	 * @param UriBuilder $uriBuilder
	 * @return Rss2\Feed
	 * @throws Exception\InvalidRss2ChannelModeException
	 * @throws Exception\MissingRss2ChannelNodeException
	 */
	public function translateNodeToFeed(NodeInterface $feedNode, UriBuilder $uriBuilder) {
		$this->uriBuilder = clone $uriBuilder;
		$feedConfiguration = $feedNode->getNodeType()->getConfiguration('feeder.rss2.feed');

		switch ($feedConfiguration['channelMode']) {
			case self::CHANNELMODE_PATH:
				$channelNode = $feedNode->getNode($feedConfiguration['channelMode']);
				if (!$channelNode instanceof NodeInterface) {
					throw new Exception\MissingRss2ChannelNodeException('No RSS2 channel node found at path ' . $feedConfiguration['channelPath'] . '.', 1398425745);
				}
			break;
			case self::CHANNELMODE_PRIMARYCONTENT:
				$channelNode = $feedNode->getPrimaryChildNode();
				if (!$channelNode instanceof NodeInterface) {
					throw new Exception\MissingRss2ChannelNodeException('No RSS2 channel node found when using primary content.', 1398427456);
				}
			break;
			case self::CHANNELMODE_SELF:
				$channelNode = $feedNode;
			break;
			default:
				throw new Exception\InvalidRss2ChannelModeException($feedConfiguration['channelMode'] . ' is no valid channel mode for RSS2 feeds.', 1398627590);
		}

		$channel = $this->translateNodeToChannel($channelNode);

		return new Rss2\Feed($channel);
	}

	/**
	 * @param NodeInterface $channelNode
	 * @return Rss2\Channel
	 * @throws Exception\InvalidRss2ChannelNodeException
	 * @todo handle description
	 * @todo handle optional properties
	 */
	public function translateNodeToChannel(NodeInterface $channelNode) {
		$this->mappedNodePropertyExtractor->reset()->initialize($channelNode, Syndicator::FORMAT_RSS2, 'channel');

		$channel = new Rss2\Channel(
			$this->getMappedProperty('title'),
			$this->getRssUri($channelNode),
			$description
		);

		$channelConfiguration = $channelNode->getNodeType()->getConfiguration('feeder.rss2.channel');
		if (is_array($channelConfiguration)) {
			$channel = new Rss2\Channel();
			$items = new \SplObjectStorage();
			$itemNodes = $this->getItemNodes($channelNode, $channelConfiguration['itemFilter'], $channelConfiguration['itemsRecursive']);

			if (sizeof($itemNodes) > 0) {
				foreach ($itemNodes as $itemNode) {
					$item = $this->translateNodeToItem($itemNode, $channelNode);
					if ($item instanceof Rss2\Item) {
						$items->add($item);
					}
				}
			}
			return new Rss2\Channel($items);
		} else {
			throw new Exception\InvalidRss2ChannelNodeException('The given channel node has no valid RSS 2 syndication configuration.', 1398427465);
		}
	}

	/**
	 * @param NodeInterface $parentNode
	 * @param string $itemFilter
	 * @param boolean $recursive
	 * @return array
	 */
	protected function getItemNodes(NodeInterface $parentNode, $itemFilter, $recursive = FALSE) {
		$itemNodes = $parentNode->getChildNodes($itemFilter);

		if ($recursive && sizeof($itemNodes) > 0) {
			foreach ($itemNodes as $itemNode) {
				$itemNodes = array_merge($itemNodes, $this->getItemNodes($itemNode, $itemFilter, $recursive));
			}
		}

		return $itemNodes;
	}

	/**
	 * @param NodeInterface $itemNode
	 * @param NodeInterface $channelNode
	 * @return Item|NULL
	 * @todo handle categories
	 * @todo handle comments: Node relation?
	 * @todo handle enclosure: Asset relation?
	 * @todo handle pubDate: lastChanged in TYPO3CR?
	 */
	public function translateNodeToItem(NodeInterface $itemNode, NodeInterface $channelNode) {
		if ($itemNode->getNodeType()->getConfiguration('feeder.rss2.item.propertyMapping')) {
			switch ($itemNode->getNodeType()->getConfiguration('feeder.rss2.item.propertyMapping.descriptionMode')) {
				case self::ITEMDESCRIPTIONMODE_PRIMARYCONTENT:
					$description = $this->collapseDescriptionNodes($itemNode->getPrimaryChildNode());
				break;
				case self::ITEMDESCRIPTIONMODE_PATH:
					$description = $this->collapseDescriptionNodes($itemNode->getNode($itemNode->getNodeType()->getConfiguration('feeder.rss2.item.propertyMapping.descriptionPath')));
				break;
				default:
					$description = '';
			}
			$categories = new ArrayCollection();
			$comments = '';
			$enclosure = NULL;
			$pubDate = '';

			return new Item(
				$itemNode->getProperty($itemNode->getNodeType()->getConfiguration('feeder.rss2.item.propertyMapping.title')),
				$this->uriBuilder->reset()->setCreateAbsoluteUri(TRUE)->uriFor('show', array('node' => $itemNode), 'Frontend\Node', 'TYPO3.Neos'),
				$description,
				$itemNode->getProperty($itemNode->getNodeType()->getConfiguration('feeder.rss2.item.propertyMapping.author')),
				$categories,
				$comments,
				$enclosure,
				$itemNode->getIdentifier(),
				$pubDate,
				new Source($channelNode->getProperty($channelNode->getNodeType()->getConfiguration('feeder.rss2.channel.propertyMapping.title')), $this->getRssUri($channelNode))
			);
		} else {
			return NULL;
		}
	}

	/**
	 * @param NodeInterface $parentNode
	 * @return string
	 */
	protected function collapseDescriptionNodes(NodeInterface $parentNode) {
		$description = '';
		$descriptionContentNodes = $parentNode->getChildNodes('TYPO3.Neos.NodeTypes:Text');
		if (sizeof($descriptionContentNodes) > 0) {
			foreach ($descriptionContentNodes as $i => $descriptionContentNode) {
				/** @var NodeInterface $descriptionContentNode */
				$description .= $descriptionContentNode->getProperty('text');

				if ($i < sizeof($descriptionContentNodes)-1) {
					$description .= "\n";
				}
			}
		}
		return $description;
	}

	/**
	 * @param NodeInterface $node
	 * @return string
	 * @todo Find out how to use UriBuilder's setFormat without throwing an exception
	 */
	protected function getRssUri(NodeInterface $node) {
		$rssNodeUri = $this->uriBuilder->reset()->setCreateAbsoluteUri(TRUE)->uriFor('show', array('node' => $node), 'Frontend\Node', 'TYPO3.Neos');
		$rssNodeUri = substr($rssNodeUri, 0, strrpos($rssNodeUri, '.')+1) . 'rss2';
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