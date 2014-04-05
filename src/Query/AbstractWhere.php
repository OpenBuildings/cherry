<?php namespace CL\Atlas\Query;

use CL\Atlas\SQL;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class AbstractWhere extends AbstractQuery
{
    /**
     * @var SQL\Join[]|null
     */
    protected $join;

    /**
     * @var SQL\Condition[]|null
     */
    protected $where;

    /**
     * @var SQL\Direction[]|null
     */
    protected $order;

    /**
     * @var SQL\IntValue|null
     */
    protected $limit;

    /**
     * @return SQL\Join[]|null
     */
    public function getJoin()
    {
        return $this->join;
    }

    /**
     * @return SQL\Condition[]|null
     */
    public function getWhere()
    {
        return $this->where;
    }

    /**
     * @return SQL\Direction[]|null
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @return SQL\IntValue|null
     */
    public function getLimit()
    {
        return $this->limit;
    }

    public function join($table, $condition, $type = null)
    {
        $table = new SQL\Aliased($table);
        $this->join []= new SQL\Join($table, $condition, $type);

        return $this;
    }

    public function joinAliased($table, $alias, $condition, $type = null)
    {
        $table = new SQL\Aliased($table, $alias);
        $this->join []= new SQL\Join($table, $condition, $type);

        return $this;
    }

    public function where(array $condition)
    {
        $this->where []= new SQL\ConditionArray($condition);

        return $this;
    }

    public function whereRaw($condition)
    {
        $parameters = array_slice(func_get_args(), 1);
        $this->where []= new SQL\Condition($condition, $parameters);

        return $this;
    }

    public function order($column, $direction = null)
    {
        $this->order []= new SQL\Direction($column, $direction);

        return $this;
    }

    public function limit($limit)
    {
        $this->limit = new SQL\IntValue($limit);

        return $this;
    }
}