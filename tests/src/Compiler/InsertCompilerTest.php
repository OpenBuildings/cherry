<?php namespace CL\Cherry\Test\Compiler;

use CL\Cherry\Test\TestCase;
use CL\Cherry\Compiler\InsertCompiler;
use CL\Cherry\Query\InsertQuery;
use CL\Cherry\Query\SelectQuery;
use CL\Cherry\SQL\SQL;

/**
 * @group compiler
 * @group compiler.insert
 */
class InsertCompilerTest extends TestCase {


	public function dataInsert()
	{
		$rows = array();
		$args = array();

		// ROW 1
		// --------------------

		$args[0] = new InsertQuery;
		$args[0]
			->type('IGNORE')
			->into('table1')
			->set(array('name' => 10, 'email' => 'email@example.com'));

		$args[1] = <<<SQL
INSERT IGNORE INTO table1 SET name = ?, email = ?
SQL;
		$rows[] = $args;


		// ROW 2
		// --------------------
		$select = new SelectQuery;
		$select
			->from('table2')
			->where(array('name' => '10'));

		$args[0] = new InsertQuery;
		$args[0]
			->into('table1')
			->columns(array('id', 'name'))
			->select($select);

		$args[1] = <<<SQL
INSERT INTO table1 (id, name) SELECT * FROM table2 WHERE (name = ?)
SQL;
		$rows[] = $args;

		// ROW 3
		// --------------------
		$args[0] = new InsertQuery;
		$args[0]
			->into('table1')
			->columns(array('id', 'name'))
			->values(array(1, 'name1'))
			->values(array(2, 'name2'));

		$args[1] = <<<SQL
INSERT INTO table1 (id, name) VALUES (?, ?), (?, ?)
SQL;
		$rows[] = $args;

		return $rows;
	}

	/**
	 * @covers CL\Cherry\Compiler\InsertCompiler::render
	 * @dataProvider dataInsert
	 */
	public function testInsert($query, $expected_sql)
	{
		$this->assertEquals($expected_sql, InsertCompiler::render($query));
	}
}