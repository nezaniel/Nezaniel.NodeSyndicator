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
use Nezaniel\Syndicator\Core\Syndicator;
use Nezaniel\Syndicator\Dto\Rss2 as Rss2;
use Nezaniel\NodeSyndicator\Translation\Exception as Exception;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Mvc\Routing\UriBuilder;
use TYPO3\Flow\Utility\Arrays;
use TYPO3\Media\Domain\Model\AssetInterface;
use TYPO3\Media\Domain\Model\ImageVariant;
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

	const ITEMGUIDMODE_PERMALINK = 'permalink';
	const ITEMGUIDMODE_IDENTIFIER = 'identifier';


	/**
	 * @param NodeInterface $feedNode
	 * @param UriBuilder $uriBuilder
	 * @return Rss2\Feed
	 * @throws Exception\InvalidRss2ChannelModeException
	 * @throws Exception\MissingRss2ChannelNodeException
	 */
	public function translateNodeToFeed(NodeInterface $feedNode, UriBuilder $uriBuilder) {
		$this->uriBuilder = clone $uriBuilder;
		$feedConfiguration = $feedNode->getNodeType()->getConfiguration('syndication.rss2.feed');

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
	 * @todo handle language
	 * @todo handle categories
	 * @todo handle cloud
	 * @todo handle text input
	 * @return Rss2\Channel
	 */
	public function translateNodeToChannel(NodeInterface $channelNode) {
		$channelConfiguration = $channelNode->getNodeType()->getConfiguration('syndication.rss2.channel');
		$this->mappedNodePropertyExtractor->reset()->initialize($channelNode, Syndicator::FORMAT_RSS2, 'channel');
		$now = new \DateTime();

		$channel = new Rss2\Channel(
			$this->getMappedProperty('title'),
			$this->getSyndicationUri($channelNode, Syndicator::FORMAT_RSS2),
			strip_tags($this->nodeService->extractDescription($channelNode, Syndicator::FORMAT_RSS2, 'channel'))
		);

		//$channel->setLanguage($channelNode->getContext()->getDimensions()['locale']);
		if (($copyright = $this->mappedNodePropertyExtractor->extractMappedProperty('copyright')) !== NULL)
			$channel->setCopyright($copyright);
		if (($managingEditor = $this->mappedNodePropertyExtractor->extractMappedProperty('managingEditor')) !== NULL)
			$channel->setManagingEditor($managingEditor);
		if (($webMaster = $this->mappedNodePropertyExtractor->extractMappedProperty('webMaster')) !== NULL)
			$channel->setWebMaster($webMaster);
		$channel->setPubDate($now);
		// insert categories here
		$channel->setGenerator('Nezaniel.NodeSyndicator powered by TYPO3.Neos');
		// insert cloud here
		if (($ttl = $this->mappedNodePropertyExtractor->extractMappedProperty('ttl')) !== NULL)
			$channel->setTtl($ttl);
		if (($image = $this->mappedNodePropertyExtractor->extractMappedProperty('image')) instanceof ImageVariant) {
			/** @var ImageVariant $image */
			$channelImage = new Rss2\Image($image->getResource()->getUri(), $channel->getTitle(), $channel->getLink(), $image->getWidth(), $image->getHeight());
			if (($imageDescription = $this->mappedNodePropertyExtractor->extractMappedProperty('imageDescription')) !== NULL)
				$channelImage->setDescription($imageDescription);
			$channel->setImage($channelImage);
		}
		if (($rating = $this->mappedNodePropertyExtractor->extractMappedProperty('rating')) !== NULL)
			$channel->setRating($rating);
		// handle text input
		if (($skipHours = $this->mappedNodePropertyExtractor->extractMappedProperty('skipHours')) !== NULL) {
			foreach (Arrays::integerExplode(',', $skipHours) as $hourToSkip) {
				$channel->addHourToSkip($hourToSkip);
			}
		}
		if (($skipDays = $this->mappedNodePropertyExtractor->extractMappedProperty('skipDays')) !== NULL) {
			foreach (explode(',', $skipDays) as $dayToSkip) {
				$channel->addDayToSkip($dayToSkip);
			}
		}
		$channel->setAtomLink($this->getSyndicationUri($channelNode, Syndicator::FORMAT_RSS2, 'channel'));

		$items = new \SplObjectStorage();
		$itemNodes = $this->nodeService->getItemNodes($channelNode, $channelConfiguration['itemFilter'], $channelConfiguration['itemsRecursive']);
		if (sizeof($itemNodes) > 0) {
			foreach ($itemNodes as $itemNode) {
				$item = $this->translateNodeToItem($itemNode, $channel);
				if ($item instanceof Rss2\Item) {
					$items->attach($item);
				}
			}
		}
		$channel->setItems($items);
		return $channel;
	}



	/**
	 * @param NodeInterface $itemNode
	 * @param Rss2\Channel $channel
	 * @return Rss2\Item|NULL
	 * @todo handle categories
	 * @todo handle pubDate: lastChanged in TYPO3CR?
	 */
	public function translateNodeToItem(NodeInterface $itemNode, Rss2\Channel $channel) {
		$this->mappedNodePropertyExtractor->reset()->initialize($itemNode, Syndicator::FORMAT_RSS2, 'item');

		$title = $this->mappedNodePropertyExtractor->extractMappedProperty('title');
		$description = $this->nodeService->extractDescription($itemNode, Syndicator::FORMAT_RSS2, 'item');

		if ($title !== NULL && $title !== '' || $description !== NULL && $description !== '') {
			$item = new Rss2\Item();
			if ($title !== NULL)
				$item->setTitle($title);
			$item->setLink($this->uriBuilder->reset()->setCreateAbsoluteUri(TRUE)->uriFor('show', array('node' => $itemNode), 'Frontend\Node', 'TYPO3.Neos'));
			if ($description !== NULL)
				$item->setDescription($description);
			if (($author = $this->mappedNodePropertyExtractor->extractMappedProperty('author')) !== NULL)
				$item->setAuthor($author);
			// handle categories
			$comments = $this->mappedNodePropertyExtractor->extractMappedProperty('comments');
			if ($comments instanceof NodeInterface) {
				$item->setComments($this->uriBuilder->reset()->setCreateAbsoluteUri(TRUE)->uriFor('show', array('node' => $comments), 'Frontend\Node', 'TYPO3.Neos'));
			} elseif (is_string($comments) && strlen($comments) > 0) {
				$item->setComments($comments);
			}
			if (($enclosure = $this->mappedNodePropertyExtractor->extractMappedProperty('enclosure')) instanceof AssetInterface) {
				/** @var AssetInterface $enclosure */
				$item->setEnclosure(new Rss2\Enclosure(
					$this->resourcePublisher->getPersistentResourceWebUri($enclosure->getResource()),
					filesize($enclosure->getResource()->getUri()),
					$enclosure->getResource()->getMediaType()
				));
			}
			$idMode = $itemNode->getNodeType()->getConfiguration('syndication.' . Syndicator::FORMAT_RSS2 . '.item.idMode');
			$item->setPermaLink(!($idMode === self::IDMODE_IDENTIFIER));
			$item->setGuid($this->translateNodeToId($itemNode, Syndicator::FORMAT_RSS2, 'item'));

			$item->setSource(new Rss2\Source(
				$channel->getTitle(),
				$channel->getLink()
			));

			return $item;
		}
		return NULL;
	}

}