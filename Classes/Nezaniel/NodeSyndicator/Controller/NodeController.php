<?php
namespace Nezaniel\NodeSyndicator\Controller;

/*                                                                          *
 * This script belongs to the TYPO3 Flow package "Nezaniel.NodeSyndicator". *
 *                                                                          *
 * It is free software; you can redistribute it and/or modify it under      *
 * the terms of the GNU General Public License, either version 3 of the     *
 * License, or (at your option) any later version.                          *
 *                                                                          *
 * The TYPO3 project - inspiring people to share!                           *
 *                                                                          */
use Nezaniel\NodeSyndicator\Dto\Atom\FeedFacade as AtomFeedFacade;
use Nezaniel\NodeSyndicator\Service\NodeInformationService;
use Nezaniel\NodeSyndicator\Translation\NodeToRss2Translator;
use Nezaniel\Syndicator\Core\Syndicator;
use Nezaniel\Syndicator\View\AtomRenderer;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Mvc\Controller\ActionController;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;
use TYPO3\TYPO3CR\Exception\PageNotFoundException;

class NodeController extends ActionController {

	/**
	 * @Flow\Inject
	 * @var NodeInformationService
	 */
	protected $nodeInformationService;

	/**
	 * @Flow\Inject
	 * @var NodeToRss2Translator
	 */
	protected $nodeToRss2Translator;


	/**
	 * @param NodeInterface $node
	 * @return string
	 * @throws PageNotFoundException if the requested node is not configured for or capable of being rendered as feed in the requested format
	 */
	public function syndicateAction(NodeInterface $node) {
		if ($this->nodeInformationService->canNodeBeSyndicated($node, $this->getSyndicationFormat())
			&& $this->nodeInformationService->isNodeToBeSyndicated($node, $this->getSyndicationFormat())) {
				switch ($this->getSyndicationFormat()) {
					case Syndicator::FORMAT_RSS2:
						$feed = $this->nodeToRss2Translator->translateNodeToFeed($node, $this->uriBuilder);
						header('Content-Type:' . Syndicator::CONTENTTYPE_RSS2);
						exit($feed->xmlSerialize());
					case Syndicator::FORMAT_ATOM:
						$feedRenderer = new AtomRenderer(new AtomFeedFacade($node));
						//header('Content-Type:' . Syndicator::CONTENTTYPE_ATOM);
						exit($feedRenderer->render());
					default:
					throw new PageNotFoundException();
				}
		}
		throw new PageNotFoundException();
	}


	/**
	 * @return string
	 */
	protected function getSyndicationFormat() {
		return $this->request->getFormat();
	}

}