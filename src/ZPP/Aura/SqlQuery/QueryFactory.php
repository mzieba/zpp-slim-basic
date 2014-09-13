<?php

namespace ZPP\Aura\SqlQuery;

class QueryFactory extends \Aura\SqlQuery\QueryFactory
{
    /**
     * @var \Aura\Sql\ExtendedPDO
     */
    protected $pdo;

    public function __construct($db, \Aura\Sql\ExtendedPDO $pdo) {
        parent::__construct($db);
        $this->pdo = $pdo;
    }
    
    /**
     * Returns a new query object.
     * @param string $query The query object type.
     * @return AbstractQuery
     */
    protected function newInstance($query)
    {
        $class = "ZPP\Aura\SqlQuery\\{$query}";

        $obj = new $class(new \Aura\SqlQuery\Quoter(
            $this->quote_name_prefix,
            $this->quote_name_suffix
        ));
        
        $obj->setPdo($this->pdo);
        return $obj;
    }
}