<?php

return array(

    'path' => 'assets/media/',

    'repertoires' => array(
        'article',
        'produit',
    ),

    'types' => array(
        'image' => array(
            'type' => 'image',
            'mimes' => array('jpeg', 'jpg','png','bmp','gif'),
        ),
        'document' => array(
            'type' => 'document',
            'mimes' => array('pdf'),
            'icone' => 'document.png',
        ),
    )

);