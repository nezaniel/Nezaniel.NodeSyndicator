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
use Nezaniel\Syndicator\Core\Syndicator;
use Nezaniel\Syndicator\Dto\Atom as Atom;

class PersonFacade extends AbstractFacade implements Atom\PersonInterface {

	/**
	 * @return string
	 */
	public function getName() {
		return $this->nodeDataExtractor->extractMappedProperty($this->getNode(), Syndicator::FORMAT_ATOM, 'person', 'name');
	}

	/**
	 * @return string
	 */
	public function getUri() {
		return $this->nodeDataExtractor->extractIdentifier($this->getNode(), Syndicator::FORMAT_ATOM, 'person');
	}

	/**
	 * @return string
	 */
	public function getEmail() {
		return $this->nodeDataExtractor->extractMappedProperty($this->getNode(), Syndicator::FORMAT_ATOM, 'person', 'email');
	}

}