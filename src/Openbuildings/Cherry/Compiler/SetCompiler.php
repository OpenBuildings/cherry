<?php namespace Openbuildings\Cherry\Compiler;

use Openbuildings\Cherry\Arr;
use Openbuildings\Cherry\SQL\SQL;
use Openbuildings\Cherry\SQL\SetSQL;
use Openbuildings\Cherry\Query\SelectQuery;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class SetCompiler extends Compiler
{
	public static function combine($items)
	{
		return Arr::join(', ', Arr::map(__NAMESPACE__."\SetCompiler::render", $items));
	}

	public static function render(SetSQL $item)
	{
		if ($item->value() instanceof SQL)
		{
			$value = $item->value()->content();
		}
		elseif ($item->value() instanceof SelectQuery)
		{
			$value = Compiler::braced(SelectCompiler::render($item->value()));
		}
		else
		{
			$value = '?';
		}

		return $item->content().' = '.$value;
	}
}
