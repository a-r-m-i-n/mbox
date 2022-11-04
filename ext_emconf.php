<?php

$EM_CONF[$_EXTKEY] = array(
    'title' => 'mbox Mail Client',
    'description' => 'TYPO3 CMS backend module to view mbox file contents, like an email client.',
    'category' => 'backend',
    'author' => 'Armin Vieweg',
    'author_email' => 'armin@v.ieweg.de',
    'author_company' => 'IW Medien GmbH | www.iwmedien.de',
    'version' => '1.0.0',
    'state' => 'stable',
    'constraints' => [
        'depends' =>
            [
                'typo3' => '11.5.0-12.9.99',
            ],
        'conflicts' => [],
        'suggests' => [],
    ],
);
