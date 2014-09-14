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

// raportowanie błędów
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

// tworzymy obiekt naszej aplikacji
$app = new \Slim\Slim([
    'templates.path' => 'src/views',
    'debug' => true
]);

// konfigurację logów
include 'config/bootstrap.logs.php';

// sesje i bd
include 'config/bootstrap.db.php';

// routing
$routes = new \ZPP\Router\Routes;
$routes->fromArray(include 'config/routes.php');
$routes->install($app);

// przykład użycia
// pełny sposób: $monolog = $app->container->get('syslog');
DEBUG && $app->syslog->addInfo('odpalenie aplikacji', [
    'ip' => $_SERVER['REMOTE_ADDR'],
]);

$app->run();
