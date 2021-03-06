<?php

namespace Harp\Query\Test\SQL;

use Harp\Query\Test\AbstractTestCase;
use Harp\Query;
use Harp\Query\SQL;

/**
 * @group sql.set
 * @coversDefaultClass Harp\Query\SQL\Set
 */
class SetTest extends AbstractTestCase
{

    public function dataConstruct()
    {
        $sql = new SQL\SQL('IF (column, 10, ?)', array(20));
        $query = new Query\Select(self::getNewDb());
        $query
            ->from('table1')
            ->where('name', 10)
            ->limit(1);

        return array(
            array('column', 20, 'column', 20, array(20)),
            array('column', $sql, 'column', $sql, array(20)),
            array('column', $query, 'column', $query, array(10)),
        );
    }

    /**
     * @dataProvider dataConstruct
     * @covers ::__construct
     * @covers ::getValue
     * @covers ::getParameters
     */
    public function testConstruct($column, $value, $expectedColumn, $expectedValue, $expectedParams)
    {
        $set = new SQL\Set($column, $value);

        $this->assertEquals($expectedColumn, $set->getContent());
        $this->assertEquals($expectedValue, $set->getValue());
        $this->assertEquals($expectedParams, $set->getParameters());
    }
}
