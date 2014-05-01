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
use TYPO3\Flow\Annotations as Flow;
use Nezaniel\NodeSyndicator\Service\MappedNodePropertyExtractor;

/**
 * An abstract translator for nodes
 */
abstract class AbstractNodeToFeedTranslator implements NodeToFeedTranslatorInterface {

	/**
	 * @Flow\Inject
	 * @var MappedNodePropertyExtractor
	 */
	protected $mappedNodePropertyExtractor;

}