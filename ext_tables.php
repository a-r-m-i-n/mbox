<?php

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
    'mbox',
    'tools',
    'mboxModule',
    '',
    [
        \T3\Mbox\Controller\MboxModuleController::class => 'index,show,downloadEml,downloadAttachment,clearMbox',
    ],
    [
        'access' => 'user,group',
        'icon' => 'EXT:mbox/Resources/Public/Icons/Extension.svg',
        'labels' => 'LLL:EXT:mbox/Resources/Private/Language/locallang_mod.xlf',
    ]
);
