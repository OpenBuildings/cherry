<?php

namespace Harp\Query\Compiler;

use Harp\Query;
use Harp\Query\Arr;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Union
{
    /**
     * Render a Select object
     * @param  Query\Select $query
     * @return string
     */
    public static function render(Query\Union $query)
    {
        return Compiler::expression(array(
            Arr::join(' UNION ', Arr::map(function (Query\Select $select) {
                return Compiler::braced(Select::render($select));
            }, $query->getSelects())),
            Compiler::word('ORDER BY', Direction::combine($query->getOrder())),
            Compiler::word('LIMIT', $query->getLimit()),
        ));
    }

    public static function parameters(Query\Union $query)
    {
        return Compiler::parameters(array(
            $query->getSelects(),
            $query->getOrder(),
        ));
    }
}
