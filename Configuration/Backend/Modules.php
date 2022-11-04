<?php

return [
    'tools_MboxModule' => [
        'parent' => 'tools',
        'access' => 'admin',
        'workspaces' => 'live',
        'iconIdentifier' => 'mbox-extension-icon',
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
