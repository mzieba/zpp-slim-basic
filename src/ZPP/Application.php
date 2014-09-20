<?php

namespace ZPP;

class Application
{
    public static function start() {
        self::bootstrapErrorReporting();
        
        $app = new \Slim\Slim();
        
        self::bootstrapLogs();
        self::bootstrapSession();
        self::bootstrapDatabase();
        self::bootstrapViews();
        self::bootstrapRouting();

        return $app->run();
    }

   
    protected static function bootstrapErrorReporting() {
        // raportowanie błędów
        error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
    }

    
    protected static function bootstrapRouting() {
        // routing
        $routes = new \ZPP\Application\Router\Routes;
        $routes->fromArray(include 'config/routes.php');
        $routes->install(\Slim\Slim::getInstance());
    }

    
    protected static function bootstrapViews() {
        \Slim\Slim::getInstance()->view(new \ZPP\Application\View\SlimExtended());
        \Slim\Slim::getInstance()->view()->setTemplatesDirectory('src/views');
    }

    
    protected static function bootstrapLogs() {
        // konfiguracja logów i monologa
        \Slim\Slim::getInstance()->container->singleton('syslog', function() {
            $fileName = 'logs/system.log';

            $logger = new \Monolog\Logger('syslog');

            // zapis do pliku
            $handler = new \Monolog\Handler\RotatingFileHandler($fileName, 14);

            // dodajemy obsługę skonfigurowanego obiektu
            $logger->pushHandler($handler);

            // zwracamy
            return $logger;
        });

        // ...lub trochę krócej
        \Slim\Slim::getInstance()->container->singleton('dblog', function() {
            return new \Monolog\Logger('database', [
                new \Monolog\Handler\RotatingFileHandler(
                    'logs/database.log', 14
                )
            ]);
        });
    }

    
    protected static function bootstrapSession() {
        // konfiguracja sesji
        \Slim\Slim::getInstance()->container->singleton('session', function() {
            $sessionFactory = new \Aura\Session\SessionFactory;
            return $sessionFactory->newInstance($_COOKIE);
        });
    }
    
    
    protected static function bootstrapDatabase() {
        // stworzenie obiektu odpowiedzialnego za połączenie z BD
        \Slim\Slim::getInstance()->container->singleton('pdo', function() {
            // położenie pliku konfiguracyjnego (dynamicznie tworzona nazwa)
            $fileName = 'config/db.' . (getenv('APP_ENV') ?: 'local') . '.php';

            // koniecznie sprawdzamy, czy istnieje!
            if (!file_exists($fileName)) {
                throw new \Exception('no db config file!');
            }

            // wczytanie tabicy do zmiennej - unikamy przestrzeni globalnej!
            $dbConfig = include $fileName;

            $dsn = sprintf('mysql:host=%s;dbname=%s;port=%d',
                    $dbConfig['host'],
                    $dbConfig['name'],
                    $dbConfig['port']
            );

            return new \Aura\Sql\ExtendedPdo(
                $dsn,
                $dbConfig['user'],
                $dbConfig['pass'],
                [
                    \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
                ]
            );
        });

        // konfiguracja queryFactory
        \Slim\Slim::getInstance()->container->singleton('query', function() {
            $queryFactory = new \ZPP\Aura\SqlQuery\QueryFactory('mysql');
            $queryFactory->setPdo(\Slim\Slim::getInstance()->pdo);
            return $queryFactory;
        });    
    }
}
