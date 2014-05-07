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
use Nezaniel\NodeSyndicator\TypoScript\AbstractFeedAdapter;
use Nezaniel\Syndicator\Dto\Atom as Atom;
use Nezaniel\Syndicator\View\Rss2InlineRenderer;
use TYPO3\Flow\Annotations as Flow;

/**
 * A TypoScript object abstraction for rendering Atom constructs
 *
 * @Flow\Scope("prototype")
 */
abstract class AbstractRss2Adapter extends AbstractFeedAdapter {

	/**
	 * @var Rss2InlineRenderer
	 */
	protected $renderer;


	/**
	 * Constructor
	 *
	 * @param \TYPO3\TypoScript\Core\Runtime $tsRuntime
	 * @param string $path
	 * @param string $typoScriptObjectName
	 */
	public function __construct(\TYPO3\TypoScript\Core\Runtime $tsRuntime, $path, $typoScriptObjectName) {
		$this->tsRuntime = $tsRuntime;
		$this->path = $path;
		$this->typoScriptObjectName = $typoScriptObjectName;
		$this->renderer = new Rss2InlineRenderer();
	}

}