<?php

namespace ZPP\Aura\SqlQuery;

class Insert extends \Aura\SqlQuery\Mysql\Insert
{
    // używając traits
    use \ZPP\Aura\SqlQuery\PdoTrait;

    public function execute() {
        // nadpisujemy kolumny w insercie
        $this->cols(array_keys($this->getBindValues()));
        
        // wykonanie zapytania
        $this->pdo->perform($this->__toString(), $this->getBindValues());

        return $this->pdo->lastInsertId();
    }
}