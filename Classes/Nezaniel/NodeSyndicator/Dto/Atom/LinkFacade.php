<?php
namespace Nezaniel\NodeSyndicator\Dto\Atom;

/*                                                                          *
 * This script belongs to the TYPO3 Flow package "Nezaniel.NodeSyndicator". *
 *                                                                          *
 * It is free software; you can redistribute it and/or modify it under      *
 * the terms of the GNU General Public License, either version 3 of the     *
 * License, or (at your option) any later version.                          *
 *                                                                          *
 * The TYPO3 project - inspiring people to share!                           *
 *                                                                          */
use Nezaniel\Syndicator\Dto\Atom as Atom;
use TYPO3\Flow\Annotations as Flow;

/**
 * The facade for handling Nodes as Atom\FeedInterfaces
 */
class LinkFacade extends AbstractFacade implements Atom\LinkInterface {

	/**
	 * @return string
	 */
	public function getHref() {
		// TODO: Implement getHref() method.
	}

	/**
	 * @return string
	 */
	public function getRel() {
		// TODO: Implement getRel() method.
	}

	/**
	 * @return string
	 */
	public function getType() {
		// TODO: Implement getType() method.
	}

	/**
	 * @return string
	 */
	public function getHreflang() {
		// TODO: Implement getHreflang() method.
	}

	/**
	 * @return string
	 */
	public function getTitle() {
		// TODO: Implement getTitle() method.
	}

	/**
	 * @return integer
	 */
	public function getLength() {
		// TODO: Implement getLength() method.
	}

}