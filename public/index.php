<?php

// kroki, jakie podejmiemy tworząc naszą aplikację
// 0. konfiguracja slim
// 1. obsługa logowania
// 2. obsługa sesji
// 3. połączenie z BD (mysql)
// 4. widoki
// 5. prosta aplikacja (z faker)

// ustalamy, że katalogiem głównym jest katalog projektu
chdir('../');

// czy jesteśmy na localhoście
define('DEBUG', 'local' === getenv('APP_ENV'));

// dołączamy definicję autoloadera
include 'vendor/autoload.php';

// tworzymy obiekt naszej aplikacji
$app = new \Slim\Slim([
    'templates.path' => 'src/views'
]);

// konfigurację logów
include 'config/bootstrap.logs.php';


// sesje i bd
include 'config/bootstrap.db.php';

// przykład użycia
// pełny sposób: $monolog = $app->container->get('syslog');
DEBUG && $app->syslog->addInfo('odpalenie aplikacji', [
    'ip' => $_SERVER['REMOTE_ADDR'],
]);

$app->get('/', function () use ($app) {
    $pageContent = $app->view()->fetch('index/index.phtml', [
        'ip' => $_SERVER['REMOTE_ADDR']
    ]);
    
    $app->render('common/layout.phtml', [
        'content' => $pageContent
    ]);
});

// przykład użycia fakera
$app->get('/faker-simple', function () use ($app) {
    $fakerFactory = new Faker\Factory();
    $generator = $fakerFactory->create('pl_PL');
    
    $users = [];
    for ($i=0; $i<10; ++$i) {
        $users[] = [
            'user_name' => $generator->firstName,
            'user_surname' => $generator->lastName,
            'user_city' => $generator->city,
            'user_birthdate' => $generator->date('Y-m-d'),
        ];
    }

    var_dump($users);
});


// faker+generowanie zapytań do bazy
$app->get('/faker-insert', function () use ($app) {
    $fakerFactory = new Faker\Factory();
    $generator = $fakerFactory->create('pl_PL');
    
    // twórz zapytania dla mysql
    $queryFactory = new \Aura\SqlQuery\QueryFactory('mysql');
    // wzorzec dla zapytań insert
    $insert = $queryFactory->newInsert();
    // szczegóły zapytania
    $insert
        ->into('user')
        ->cols(['user_name', 'user_surname', 'user_city', 'user_birthdate']);
    
    // przygotuj zapytanie
    $insertStatement = $app->pdo->prepare($insert->__toString());
    // równoznaczne: $insertStatement = $app->pdo->prepare((string) $insert);
        
    for ($i=0; $i<10; ++$i) {
        // dane
        $user = [
            'user_name' => $generator->firstName,
            'user_surname' => $generator->lastName,
            'user_city' => $generator->city,
            'user_birthdate' => $generator->date('Y-m-d'),
        ];
        
        // wykonaj przygotowane zapytanie dla podanych danych
        $insertStatement->execute($user);
        print 'Dodano rekord o user_id = ' . $app->pdo->lastInsertID() . '<br>';
    }
    
    print 'ok';
});

// faker+generowanie zapytań do bazy
$app->get('/faker-insert-improved', function () use ($app) {
    $fakerFactory = new Faker\Factory();
    $generator = $fakerFactory->create('pl_PL');
    
    $insert = $app->query->newInsert('user');
    
    for ($i=0; $i<10; ++$i) {
        // dane
        $user = [
            'user_name' => $generator->firstName,
            'user_surname' => $generator->lastName,
            'user_city' => $generator->city,
            'user_birthdate' => $generator->date('Y-m-d'),
        ];
        
        // wykonaj przygotowane zapytanie dla podanych danych
        $id = $insert->bindValues($user)->execute();
        print 'Dodano rekord o user_id = ' . $id . '<br>';
    }
    
    print 'ok';
});


// faker+generowanie zapytań do bazy
$app->get('/faker-saved', function () use ($app) {
    // twórz zapytania dla mysql
    $queryFactory = new \Aura\SqlQuery\QueryFactory('mysql');
    // wyświetl
    $select = $queryFactory->newSelect();
    $select
        ->cols(['*'])
        ->from('user')
        ->where('user_name LIKE ? AND user_name LIKE ?', 'W%', '%a');

        //->where('user_name LIKE :name_begins')
        //->where('user_name LIKE :name_ends')
        //->bindValues(['name_begins' => 'W%', 'name_ends' => '%a']);
    
    $rows = $app->pdo->fetchAssoc($select->__toString(), $select->getBindValues());
    
    var_dump($rows);
});

// wersja maksymalnie uproszczona
$app->get('/faker-saved-improved', function () use ($app) {
    $list = $app->view()->fetch('faker/list.phtml', [
        'users' => $app->query->newSelect('user')->fetchAssoc()
    ]);
    
    $app->render('common/layout.phtml', [
        'content' => $list
    ]);
});

// wersja maksymalnie uproszczona
$app
    ->map('/faker-search(/:query(/:page))', function ($query = '', $page = 1) use ($app) {
        $findByName = $app->request()->params('user_name', '');
        $list = '';
        $perPage = 10;

        $form = $app->view()->fetch('faker/search.phtml', [
            'query' => $query
        ]);

        // jeżeli formularz został wysłany
        if ($findByName) {
            $app->redirect($app->urlFor('faker-search', ['query' => $findByName, 'page' => 1]));
        } else if ($query) {
            $users = $app->query
                ->newSelect('user')
                ->where('user_name LIKE ?', '%' . $query . '%')
                ->limit($perPage)
                ->offset(($page-1) * $perPage);
            
            $list = $app->view()->fetch('faker/list.phtml', [
                'users' => $users->fetchAssoc(),
                'pagePrev' => $app->urlFor('faker-search', ['query' => $query, 'page' => $page-1]),
                'pageNext' => $app->urlFor('faker-search', ['query' => $query, 'page' => $page+1]),
                'page' => $page,
            ]);
        }

        $app->render('common/layout.phtml', [
            'content' => $form . $list
        ]);
    })
    ->via('GET', 'POST')
    ->conditions([
        'page' => '\d+',
    ])
    ->name('faker-search');



$app->run();


/*

$routes = [
    'hello-name' => [
        'route' => '/hello/:name',
        'controller' => 'Hello',
        'action' => 'hello',
        'defaults' => [
            'name' => 'John'
        ],
        'method' => [
            'get'
        ],
        'conditions' => [
            'name' => '\w+'
        ]
    ]
];


*/