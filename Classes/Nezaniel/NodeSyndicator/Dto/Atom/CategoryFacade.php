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

class CategoryFacade extends AbstractFacade implements Atom\CategoryInterface {

	/**
	 * @return string
	 */
	public function getTerm() {
		return $this->nodeDataExtractor->extractMappedProperty($this->getNode(), Syndicator::FORMAT_ATOM, 'category', 'term');
	}

	/**
	 * @return string
	 */
	public function getScheme() {
		return $this->nodeDataExtractor->extractMappedProperty($this->getNode(), Syndicator::FORMAT_ATOM, 'category', 'scheme');
	}

	/**
	 * @return string
	 */
	public function getLabel() {
		return $this->nodeDataExtractor->extractMappedProperty($this->getNode(), Syndicator::FORMAT_ATOM, 'category', 'label');
	}

}