<?php
namespace Nezaniel\NodeSyndicator\TypoScript\Atom;

/*                                                                          *
 * This script belongs to the TYPO3 Flow package "Nezaniel.NodeSyndicator". *
 *                                                                          *
 * It is free software; you can redistribute it and/or modify it under      *
 * the terms of the GNU General Public License, either version 3 of the     *
 * License, or (at your option) any later version.                          *
 *                                                                          *
 * The TYPO3 project - inspiring people to share!                           *
 *                                                                          */
use Nezaniel\Syndicator\View\AtomInlineRenderer;
use TYPO3\Flow\Annotations as Flow;

/**
 * A TypoScript object implementation to render Atom Source constructs
 *
 * @Flow\Scope("prototype")
 */
class SourceImplementation extends FeedImplementation {

	/**
	 * @return string
	 */
	public function renderTitle() {
		return $this->renderText('title', 'title');
	}

	/**
	 * @var string
	 */
	protected $feedMode = AtomInlineRenderer::FEEDMODE_SOURCE;

}