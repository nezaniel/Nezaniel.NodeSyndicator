<?php
namespace Nezaniel\NodeSyndicator;

/*                                                                          *
 * This script belongs to the TYPO3 Flow package "Nezaniel.NodeSyndicator". *
 *                                                                          *
 * It is free software; you can redistribute it and/or modify it under      *
 * the terms of the GNU Lesser General Public License, either version 3     *
 * of the License, or (at your option) any later version.                   *
 *                                                                          *
 * The TYPO3 project - inspiring people to share!                           *
 *                                                                          */
use TYPO3\Flow\Package\Package as BasePackage;
use TYPO3\Flow\Annotations as Flow;

/**
 * Package base class of the Nezaniel.NodeSyndicator package.
 *
 * @Flow\Scope("singleton")
 */
class Package extends BasePackage {

	const VERSION = '0.1.0';

}