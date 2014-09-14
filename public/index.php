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
\ZPP\Application::start();
