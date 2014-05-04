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
use Nezaniel\NodeSyndicator\TypoScript\AbstractFeedFacade;
use Nezaniel\Syndicator\Dto\Atom as Atom;
use Nezaniel\Syndicator\View\AtomInlineRenderer;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Media\Domain\Model\ImageVariant;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;

/**
 * A TypoScript object implementation to render
 *
 * @Flow\Scope("prototype")
 */
abstract class AbstractAtomFacade extends AbstractFeedFacade {

	/**
	 * @var AtomInlineRenderer
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
		$this->renderer = new AtomInlineRenderer();
	}


	/**
	 * Renders the TypoScript value at the given property path as an Atom Text construct
	 *
	 * @param string $propertyPath
	 * @param string $nodeProperty
	 * @return Atom\Text
	 */
	protected function renderText($propertyPath, $nodeProperty = 'text') {
		$value = $this->tsValue($propertyPath);
		$content = NULL;
		if (is_string($value)) {
			$content = $value;
		} elseif ($value instanceof NodeInterface && $value->hasProperty($nodeProperty)) {
			$content = $value->getProperty($nodeProperty);
		}

		if ($content === '' || $content === NULL) {
			return '';
		}
		$text = new Atom\Text(
			($content === strip_tags($content)) ? Atom\Text::TYPE_TEXT : Atom\Text::TYPE_XHTML,
			$content
		);
		$text->setTagName($propertyPath);

		return $text->xmlSerialize();
	}

	/**
	 * @param string $propertyPath
	 * @return string
	 */
	protected function renderImage($propertyPath) {
		$value = $this->tsValue($propertyPath);

		if (is_string($value)) {
			return $value;
		} elseif ($value instanceof ImageVariant) {
			return $this->resourcePublisher->getPersistentResourceWebUri($value->getResource());
		} elseif ($value instanceof NodeInterface && $value->getNodeType()->isOfType('TYPO3.Neos.NodeTypes:Image')) {
			if ($value->hasProperty('image')) {
				return $this->resourcePublisher->getPersistentResourceWebUri($value->getProperty('image')->getResource());
			}
		}
		return '';
	}

}