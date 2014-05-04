<?php
namespace Nezaniel\NodeSyndicator\TypoScript\Atom;

/*                                                                         *
 * This script belongs to the TYPO3 Flow package "Nezaniel.NodeSyndicator" *
 *                                                                         *
 * It is free software; you can redistribute it and/or modify it under     *
 * the terms of the GNU General Public License, either version 3 of the    *
 * License, or (at your option) any later version.                         *
 *                                                                         *
 * The TYPO3 project - inspiring people to share!                          *
 *                                                                         */
use Nezaniel\Syndicator\Dto\Atom as Atom;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\TypoScript\TypoScriptObjects\AbstractArrayTypoScriptObject;

/**
 * A TypoScript object implementation to render
 *
 * @Flow\Scope("prototype")
 */
class PersonImplementation extends AbstractArrayTypoScriptObject implements Atom\PersonInterface {

	/**
	 * @return string
	 */
	public function getName() {
		return $this->tsValue('name');
	}

	/**
	 * @return string
	 */
	public function getUri() {
		return $this->tsValue('uri');
	}

	/**
	 * @return string
	 */
	public function getEmail() {
		return $this->tsValue('email');
	}


	/**
	 * @return Atom\PersonInterface
	 */
	public function evaluate() {
		return $this;
	}

}