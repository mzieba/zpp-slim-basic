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
$app = new \Slim\Slim();

// konfiguracja logów i monologa
$app->container->singleton('syslog', function() {
    $fileName = 'logs/system.log';
    
    $logger = new Monolog\Logger('syslog');
    
    // zapis do pliku
    $handler = new Monolog\Handler\RotatingFileHandler($fileName, 14);

    // dodajemy obsługę skonfigurowanego obiektu
    $logger->pushHandler($handler);
    
    // zwracamy
    return $logger;
});

// ...lub trochę krócej
$app->container->singleton('dblog', function() {
    return new Monolog\Logger('database', [
        new Monolog\Handler\RotatingFileHandler(
            'logs/database.log', 14
        )
    ]);
});

// przykład użycia
// pełny sposób: $monolog = $app->container->get('syslog');
DEBUG && $app->syslog->addInfo('odpalenie aplikacji', [
    'ip' => $_SERVER['REMOTE_ADDR'],
]);


// konfiguracja sesji
$app->container->singleton('session', function() {
    $sessionFactory = new \Aura\Session\SessionFactory;
    return $sessionFactory->newInstance($_COOKIE);
});


$app
    ->get(              // metoda http (get, post, put, delete, option)
    '/hello/:name',     // wzorzec adresu
    function ($name) { // funkcja + argumenty
        echo "Hello, $name"; // obsługa żądania
    }
)->name('hello'); // nazwa naszej drogi ;-)


$app->get('/faker', function () {
    $fakerFactory = new Faker\Factory();
    $generator = $fakerFactory->create('pl_PL');
    
    $users = [];
    for ($i=0; $i<10; ++$i) {
        $users[] = array(
            'name' => $generator->name
        );
    }
    
    var_dump($users);
});



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