<?php
namespace Nezaniel\NodeSyndicator\Dto;

/*                                                                          *
 * This script belongs to the TYPO3 Flow package "Nezaniel.NodeSyndicator". *
 *                                                                          *
 * It is free software; you can redistribute it and/or modify it under      *
 * the terms of the GNU General Public License, either version 3 of the     *
 * License, or (at your option) any later version.                          *
 *                                                                          *
 * The TYPO3 project - inspiring people to share!                           *
 *                                                                          */

use Nezaniel\NodeSyndicator\Domain\Service\NodeDataExtractor;
use Nezaniel\NodeSyndicator\Dto\Factory\FacadeFactory;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;

/**
 * An abstract facade for handling Nodes as syndication DTOs
 */
class AbstractFacade  {

	const CONFIGURATIONMODE_ENTITY = 'entity';
	const CONFIGURATIONMODE_ENTITYCOLLECTION = 'entityCollection';
	const CONFIGURATIONMODE_NODE = 'node';
	const CONFIGURATIONMODE_NODECOLLECTION = 'nodeCollection';


	/**
	 * @var NodeInterface
	 */
	protected $node;

	/**
	 * @Flow\Inject
	 * @var NodeDataExtractor
	 */
	protected $nodeDataExtractor;

	/**
	 * @Flow\Inject
	 * @var FacadeFactory
	 */
	protected $facadeFactory;


	/**
	 * @param NodeInterface $node
	 */
	public function __construct(NodeInterface $node) {
		$this->node = $node;
	}


	/**
	 * @return NodeInterface
	 */
	public function getNode() {
		return $this->node;
	}

}