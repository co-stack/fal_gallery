<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'FAL Gallery',
    'description' => 'Easy to use folder based gallery with separate single-, category- and list view that supports any TYPO3 FAL driver. Official successor of wt_gallery',
    'category' => 'plugin',
    'author' => 'Oliver Eglseder',
    'author_email' => 'php@vxvr.de',
    'author_company' => 'co-stack.com',
    'state' => 'stable',
    'internal' => '',
    'uploadfolder' => '0',
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '3.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.0-9.5.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
