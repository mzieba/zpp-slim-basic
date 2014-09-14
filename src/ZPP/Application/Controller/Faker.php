<?php

namespace ZPP\Application\Controller;

class Faker extends \ZPP\Application\Controller\Controller
{
    public function index() {
        $segment = $this->getApp()->session->getSegment(__CLASS__);

        $this->render('faker/list.phtml', [
            'users' => $this->getApp()->query->newSelect('user')->fetchAssoc(),
            'flashMessage' => $segment->getFlash('info')
        ]);
    }


    public function standardInsert($numberToAdd = 10) {
        $app = $this->getApp();

        $fakerFactory = new \Faker\Factory();
        $generator = $fakerFactory->create('pl_PL');
    
        // twórz zapytania dla mysql
        $queryFactory = new \Aura\SqlQuery\QueryFactory('mysql');
        // wzorzec dla zapytań insert
        $insert = $queryFactory->newInsert();
        // szczegóły zapytania
        $insert
            ->into('user')
            ->cols([
                'user_name',
                'user_surname',
                'user_city',
                'user_birthdate'
            ]);

        // przygotuj zapytanie
        $insertStatement = $app->pdo->prepare($insert->__toString());
        // równoznaczne: $insertStatement = $app->pdo->prepare((string) $insert);

        for ($i=0; $i<$numberToAdd; ++$i) {
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
    }
    
    
    public function improvedInsert($numberToAdd = 10) {
        $app = $this->getApp();

        $fakerFactory = new \Faker\Factory();
        $generator = $fakerFactory->create('pl_PL');

        $insert = $app->query->newInsert('user');
        
        $added = 0;
        for ($i=0; $i<$numberToAdd; ++$i) {
            $user = [
                'user_name' => $generator->firstName,
                'user_surname' => $generator->lastName,
                'user_city' => $generator->city,
                'user_birthdate' => $generator->date('Y-m-d'),
            ];

            // wykonaj przygotowane zapytanie dla podanych danych
            $added += $insert->bindValues($user)->execute() ? 1 : 0;
        }

        $segment = $app->session->getSegment(__CLASS__);
        $segment->setFlash('info', 'Dodano ' . $added . ' nowych wpisów.');
        $app->redirect($app->urlFor('faker'));
    }
    
    public function search($query = '', $page = 1) {
        $app = $this->getApp();

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
    }
}