<?php

// mysql: homestead/secret
// 

// 0. slim
// 1. logowanie
// 2. obsługa sesji
// 3. połączenie z BD (mysql)
// 4. widoki
// 5. prosta aplikacja (routing, generowanie linków)

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
$sessionFactory = new \Aura\Session\SessionFactory;
$session = $sessionFactory->newInstance($_COOKIE);

// dołożyć komentarze
// dużo komentarzy





$segment = $session->getSegment('pierwszySegment');

$app
    ->get(              // metoda http (get, post, put, delete, option)
    '/hello/:name',     // wzorzec adresu
    function ($name) { // funkcja + argumenty
        echo "Hello, $name"; // obsługa żądania
    }
)->name('hello');

var_dump($app->urlFor('hello', ['name' => 'ala']));


$app->get('/wiek/:name/:age', function ($name, $age) {
    echo "$name ma $age lat";
});

$app->get('/artykul/:tytul-:id(/:strona)', function () {
    var_dump(func_get_args());
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