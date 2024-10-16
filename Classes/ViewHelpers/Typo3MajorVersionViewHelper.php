<?php

namespace T3\Mbox\ViewHelpers;

use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class Typo3MajorVersionViewHelper extends AbstractViewHelper
{
    protected $escapeOutput = false;
    protected $escapeChildren = false;

    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): string {
        /** @var Typo3Version $typo3Version */
        $typo3Version = GeneralUtility::makeInstance(Typo3Version::class);

        return $typo3Version->getMajorVersion();
    }
}
