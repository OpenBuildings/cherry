<?php

namespace Harp\Query\Test;

use Harp\Query\SQL;

/**
 * @group query
 * @coversDefaultClass Harp\Query\AbstractWhere
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class AbstractWhereTest extends AbstractTestCase
{
    /**
     * @covers ::join
     * @covers ::joinAliased
     * @covers ::getJoin
     * @covers ::setJoin
     * @covers ::clearJoin
     */
    public function testJoin()
    {
        $query = $this->getMock('Harp\Query\AbstractWhere', array('sql', 'getParameters'), array(self::getDb()));

        $query
            ->join('table1', array('col' => 'col2'))
            ->joinAliased('table2', 'alias2', 'USING (col1)', 'LEFT');

        $expected = array(
            new SQL\Join(new SQL\Aliased('table1'), array('col' => 'col2')),
            new SQL\Join(new SQL\Aliased('table2', 'alias2'), 'USING (col1)', 'LEFT'),
        );

        $this->assertEquals($expected, $query->getJoin());

        $query->clearJoin();

        $this->assertEmpty($query->getJoin());

        $query->setJoin($expected);

        $this->assertEquals($expected, $query->getJoin());
    }

    /**
     * @covers ::where
     * @covers ::whereIn
     * @covers ::whereNot
     * @covers ::whereLike
     * @covers ::whereRaw
     * @covers ::getWhere
     * @covers ::setWhere
     * @covers ::clearWhere
     */
    public function testWhere()
    {
        $query = $this->getMock('Harp\Query\AbstractWhere', array('sql', 'getParameters'), array(self::getDb()));

        $query
            ->where('test1', 2)
            ->whereIn('test2', array(2, 3))
            ->whereNot('test4', '123')
            ->whereLike('test5', '123%')
            ->whereRaw('column = ? OR column = ?', array(10, 20))
            ->where('test3', 3);

        $expected = array(
            new SQL\ConditionIs('test1', 2),
            new SQL\ConditionIn('test2', array(2, 3)),
            new SQL\ConditionNot('test4', '123'),
            new SQL\ConditionLike('test5', '123%'),
            new SQL\Condition(null, 'column = ? OR column = ?', array(10, 20)),
            new SQL\ConditionIs('test3', 3),
        );

        $this->assertEquals($expected, $query->getWhere());

        $query->clearWhere();

        $this->assertEmpty($query->getWhere());

        $query->setWhere($expected);

        $this->assertEquals($expected, $query->getWhere());
    }

    /**
     * @covers ::where
     * @expectedException InvalidArgumentException
     */
    public function testWhereInInvalid()
    {
        $query = $this->getMock('Harp\Query\AbstractWhere', array('sql', 'getParameters'), array(self::getDb()));

        $query->where('test1', array(2, 3));
    }
}
