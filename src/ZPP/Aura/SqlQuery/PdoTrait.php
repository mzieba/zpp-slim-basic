<?php

namespace ZPP\Aura\SqlQuery;

trait PdoTrait
{
    /**
     * @var \Aura\Sql\ExtendedPDO
     */
    protected $pdo;

    /**
     * @param \Aura\Sql\ExtendedPDO $pdo
     */
    public function setPdo(\Aura\Sql\ExtendedPDO $pdo) {
        $this->pdo = $pdo;
    }
}