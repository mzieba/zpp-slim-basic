<?php

namespace ZPP\Aura\SqlQuery;

class Select extends \Aura\SqlQuery\Mysql\Select
{
    use \ZPP\Aura\SqlQuery\PdoTrait;

    public function fetchAssoc() {
        if (!count($this->cols)) {
            $this->cols(['*']);
        }
        // wykonanie zapytania
        return $this->pdo->fetchAssoc($this->__toString(), $this->getBindValues());
    }
}
