<?php

$EM_CONF[$_EXTKEY] = array(
    'title' => 'mbox Mail Client',
    'description' => 'TYPO3 CMS backend module to view mbox file contents, like an email client.',
    'category' => 'backend',
    'author' => 'Armin Vieweg',
    'author_email' => 'armin@v.ieweg.de',
    'author_company' => 'IW Medien GmbH | www.iwmedien.de',
    'version' => '4.0.0',
    'state' => 'stable',
    'constraints' => [
        'depends' =>
            [
                'typo3' => '13.4.0-14.3.99',
            ],
        'conflicts' => [],
        'suggests' => [],
    ],
);
