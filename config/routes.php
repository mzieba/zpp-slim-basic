<?php

return [
    
    'main' => [
        'route' => '/',
        'controller' => 'Index',
        'action' => 'index',
    ],

    'hello' => [
        'route' => '/hello(/:name)',
        'controller' => 'Index',
        'action' => 'hello',
        'defaults' => [
            'name' => 'John'
        ],
        'methods' => [
            'get', 'post'
        ],
        'conditions' => [
            'name' => '\w+'
        ]
    ],
    
    'faker' => [
        'route' => '/faker',
        'controller' => 'Faker',
        'action' => 'index',
    ],
    
    'faker-standard-insert' => [
        'route' => '/faker/standard-insert/:number',
        'controller' => 'Faker',
        'action' => 'standardinsert',
        'conditions' => [
            'number' => '\d+'
        ]
    ],
    
    'faker-improved-insert' => [
        'route' => '/faker/improved-insert/:number',
        'controller' => 'Faker',
        'action' => 'improvedinsert',
        'conditions' => [
            'number' => '\d+'
        ]
    ],
    
    'faker-search' => [
        'route' => '/faker/search(/)(:query(/:page))',
        'controller' => 'Faker',
        'action' => 'search',
        'conditions' => [
            'page' => '\d+'
        ],
        'methods' => [
            'get', 'post'
        ],
    ],

];
