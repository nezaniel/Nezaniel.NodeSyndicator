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
use Nezaniel\Syndicator\Dto\Atom as Atom;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\TypoScript\TypoScriptObjects\AbstractCollectionImplementation;

/**
 * Implementation of an entry collection renderer for Atom syndication via TypoScript
 *
 * @Flow\Scope("prototype")
 */
class EntryCollectionImplementation extends AbstractCollectionImplementation {

	/**
	 * Arranges the collection Atom\Entry nodes as an ObjectStorage
	 *
	 * @return \SplObjectStorage()
	 * @throws \TYPO3\TypoScript\Exception
	 */
	public function evaluate() {
		$collection = $this->getCollection();
		$entries = new \SplObjectStorage();

		if ($collection !== NULL) {
			$this->numberOfRenderedNodes = 0;
			$itemName = $this->getItemName();
			if ($itemName === NULL) {
				throw new \TYPO3\TypoScript\Exception('The Collection needs an itemName to be set.', 1344325771);
			}
			$iterationName = $this->getIterationName();
			$collectionTotalCount = count($collection);
			foreach ($collection as $collectionElement) {
				$context = $this->tsRuntime->getCurrentContext();
				$context[$itemName] = $collectionElement;
				if ($iterationName !== NULL) {
					$context[$iterationName] = $this->prepareIterationInformation($collectionTotalCount);
				}
				$this->tsRuntime->pushContextArray($context);
				$entry = $this->tsRuntime->render($this->path . '/itemRenderer');
				if ($entry instanceof Atom\EntryInterface) {
					$entries->attach($entry);
					$this->numberOfRenderedNodes++;
				}
				$this->tsRuntime->popContext();
			}
		}

		return $entries;
	}

}