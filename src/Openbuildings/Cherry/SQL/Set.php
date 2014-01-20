<?php namespace Openbuildings\Cherry;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class SQL_Set extends SQL
{
	protected $value;

	function __construct($column, $value)
	{
		$this->value = $value;
		parent::__construct($column);
	}

	public function parameters()
	{
		if ($this->value instanceof Parametrised)
		{
			return $this->value->parameters();
		}

		return array($this->value);
	}

	public function value()
	{
		return $this->value;
	}
}
