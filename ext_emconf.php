<?php

$EM_CONF[$_EXTKEY] = array(
    'title' => 'mbox Mail Client',
    'description' => 'TYPO3 CMS backend module to view mbox file contents, like an email client.',
    'category' => 'backend',
    'author' => 'Armin Vieweg',
    'author_email' => 'vieweg@iwkoeln.de',
    'version' => '1.0.0-dev',
    'state' => 'alpha',
    'constraints' => [
        'depends' =>
            [
                'typo3' => '10.4.0-11.5.99',
            ],
        'conflicts' => [],
        'suggests' => [],
    ],
);
