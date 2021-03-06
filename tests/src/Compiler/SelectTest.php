<?php

namespace Harp\Query\Test\Compiler;

use Harp\Query\Test\AbstractTestCase;
use Harp\Query\Compiler;
use Harp\Query;
use Harp\Query\SQL\SQL;

/**
 * @group compiler
 * @group compiler.select
 * @coversDefaultClass Harp\Query\Compiler\Select
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class SelectTest extends AbstractTestCase
{
    /**
     * @covers ::render
     * @covers ::parameters
     */
    public function testCompile()
    {
        $select2 = new Query\Select(self::getDb());
        $select2
            ->from('one')
            ->type('DISTINCT')
            ->join('table1', new SQL('USING (col1, col2)'))
            ->where('table1.name', 'small');

        $select = new Query\Select(self::getDb());
        $select
            ->from('bigtable')
            ->from('smalltable', 'alias')
            ->from($select2, 'select_alias')
            ->column('col1')
            ->column(new SQL('IF(name = ?, "big", "small")', array(10)), 'type')
            ->column('col3', 'alias_col')
            ->where('bigtable.test', 'value')
            ->whereNot('alias.test', 'some bad val')
            ->whereRaw('test_statement = IF ("test", ?, ?)', array('val1', 'val2'))
            ->join('table2', array('col1' => 'col2'))
            ->whereRaw('type > ? AND type < ? AND base IN ?', array(10, 20, array('1', '2', '3')))
            ->having('test', 'value2')
            ->havingRaw('type > ? AND base IN ?', array(20, array('5', '6', '7')))
            ->limit(10)
            ->offset(8)
            ->order('type', 'ASC')
            ->order('base')
            ->group('base', 'ASC')
            ->group('type');

        $expectedSql = <<<SQL
SELECT `col1`, IF(name = ?, "big", "small") AS `type`, `col3` AS `alias_col` FROM `bigtable`, `smalltable` AS `alias`, (SELECT DISTINCT * FROM `one` JOIN `table1` USING (col1, col2) WHERE (`table1`.`name` = ?)) AS `select_alias` JOIN `table2` ON `col1` = `col2` WHERE (`bigtable`.`test` = ?) AND (`alias`.`test` != ?) AND (test_statement = IF ("test", ?, ?)) AND (type > ? AND type < ? AND base IN (?, ?, ?)) GROUP BY `base` ASC, `type` HAVING (`test` = ?) AND (type > ? AND base IN (?, ?, ?)) ORDER BY `type` ASC, `base` LIMIT 10 OFFSET 8
SQL;

        $this->assertEquals($expectedSql, Compiler\Select::render($select));

        $expectedParameters = array(
            10,
            'small',
            'value',
            'some bad val',
            'val1',
            'val2',
            '10',
            '20',
            '1',
            '2',
            '3',
            'value2',
            '20',
            '5',
            '6',
            '7',
        );

        $this->assertEquals($expectedParameters, Compiler\Select::parameters($select));
    }
}
