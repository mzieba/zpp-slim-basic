<?php

namespace ZPP\Aura\SqlQuery;

class QueryFactory extends \Aura\SqlQuery\QueryFactory
{
    use PdoTrait;
    
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
    
    /**
     * Returns a new INSERT object.
     * @param string $tableName
     * @return Common\InsertInterface
     */
    public function newInsert($tableName = null)
    {
        $insert = parent::newInsert();
        if (null !== $tableName) {
            $insert->into($tableName);
        }
        return $insert;
    }
    
    /**
     * Returns a new SELECT object.
     * @param string $tableName
     * @param array $cols
     * @return Common\SelectInterface
     */
    public function newSelect($tableName = null, $cols = ['*'])
    {
        $select = parent::newSelect();

        if (null !== $tableName) {
            $select->from($tableName);
        }
        if (null !== $cols) {
            $select->cols($cols);
        }

        return $select;
    }
}