<?php

namespace ZPP\Aura\SqlQuery;

class Insert extends \Aura\SqlQuery\Mysql\Insert
{
    use \ZPP\Aura\SqlQuery\PdoTrait;

    public function execute() {
        $this->cols(array_keys($this->getBindValues()));
        return $this->pdo->perform($this->__toString(), $this->getBindValues());
    }
}