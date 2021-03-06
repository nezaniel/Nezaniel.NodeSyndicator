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
use TYPO3\TypoScript\TypoScriptObjects\AbstractTypoScriptObject;

/**
 * A TypoScript object implementation to render Atom Persons
 *
 * @Flow\Scope("prototype")
 */
class PersonImplementation extends AbstractTypoScriptObject {

	/**
	 * @return Atom\PersonInterface
	 */
	public function evaluate() {
		if (($name = $this->tsValue('name')) !== NULL) {
			$person = new Atom\Person(
				$this->tsValue('name'),
				$this->tsValue('uri'),
				$this->tsValue('email')
			);
			$person->setTagName($this->tsValue('type') !== NULL ? $this->tsValue('type') : 'author');
			return $person->xmlSerialize();
		}
		return '';
	}

}