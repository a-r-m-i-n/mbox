<?php

/** @var array<string, mixed> $extensionConfiguration */

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\GeneralUtility;

$extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('mbox');
if ($extensionConfiguration['enableBackendModule'] === '0') {
    return [];
}

$iconIdentifier = GeneralUtility::makeInstance(Typo3Version::class)->getMajorVersion() >= 14
    ? 'mbox-module-icon'
    : 'mbox-extension-icon';

return [
    'tools_MboxModule' => [
        'parent' => 'tools',
        'access' => 'admin',
        'workspaces' => 'live',
        'iconIdentifier' => $iconIdentifier,
        'path' => '/module/tools/MboxMboxmodule',
        'labels' => 'LLL:EXT:mbox/Resources/Private/Language/locallang_mod.xlf',
        'extensionName' => 'Mbox',
        'controllerActions' => [
            \T3\Mbox\Controller\MboxModuleController::class => [
                'index',
                'show',
                'downloadEml',
                'downloadAttachment',
                'clearMbox',
            ],
        ],
    ],
];
