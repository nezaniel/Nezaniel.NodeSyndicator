<?php
namespace Nezaniel\NodeSyndicator\Dto\Factory;

/*                                                                          *
 * This script belongs to the TYPO3 Flow package "Nezaniel.NodeSyndicator". *
 *                                                                          *
 * It is free software; you can redistribute it and/or modify it under      *
 * the terms of the GNU General Public License, either version 3 of the     *
 * License, or (at your option) any later version.                          *
 *                                                                          *
 * The TYPO3 project - inspiring people to share!                           *
 *                                                                          */
use Nezaniel\NodeSyndicator\Dto as Facade;
use Nezaniel\Syndicator\Core\Syndicator;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;

/**
 * An abstract facade for handling Nodes as syndication DTOs
 *
 * @Flow\Scope("singleton")
 */
class FacadeFactory {

	/**
	 * @param NodeInterface $node
	 * @param string        $syndicationType
	 * @param string        $type
	 * @return Facade\AbstractFacade|NULL
	 */
	public function create(NodeInterface $node, $syndicationType, $type) {
		switch ($syndicationType) {
			case Syndicator::FORMAT_ATOM:
				switch ($type) {
					case 'person':
						return new Facade\Atom\PersonFacade($node);
					case 'category':
						return new Facade\Atom\CategoryFacade($node);
					case 'entry':
						return new Facade\Atom\EntryFacade($node);
					case 'link':
						return new Facade\Atom\LinkFacade($node);
					default:
						return NULL;
				}
			break;
			case Syndicator::FORMAT_RSS2:
			break;
			default:
				return NULL;
		}
	}

}