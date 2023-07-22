<?php
/**
 * File containing the ezpTopologicalSortTest class
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */

class ezpTopologicalSortTest extends ezpTestCase
{
    protected $data = [];

    public function setUp()
    {
        $this->data['simple'] = new ezpTopologicalSort(
            ['a' => null, 'c' => 'b', 'b' => 'a', 'd' => 'b', 'e' => ['d', 'c']] );
        $this->data['complex'] = new ezpTopologicalSort(
            ['a' => null, 'c' => 'b', 'b' => 'a', 'd' => 'b', 'e' => ['d', 'c'], 'f' => range( 'a', 'e' ), 'g' => range( 'h', 'k' ), 'h' => ['j', 'k'], 'k' => 'j', 'j' => 'e', 'l' => 'm', 'm' => 'n', 'o' => 'p', 'p' => 'g'] );
        $this->data['keep-order'] = new ezpTopologicalSort(
            array_fill_keys(
                range( 'a', 'z' ),
                null ) );
        $this->data['empty'] = new ezpTopologicalSort( [] );
        $this->data['cycle'] = new ezpTopologicalSort(
            ['a' => 'b', 'b' => 'a'] );
    }

    public function testSimpleSort()
    {
        $c = null;
        $b = null;
        $a = null;
        $d = null;
        $e = null;
        $result = $this->data['simple']->sort();

        foreach ( range( 'a', 'e' ) as $letter )
            ${$letter} = array_search( $letter, $result );

        static::assertSame(5, is_countable($result) ? count( $result ) : 0);
        static::assertLessThan($c, $b);
        static::assertLessThan($b, $a);
        static::assertLessThan($d, $b);
        static::assertLessThan($e, $d);
        static::assertLessThan($e, $c);
    }

    public function testComplexSort()
    {
        $c = null;
        $b = null;
        $a = null;
        $d = null;
        $e = null;
        $f = null;
        $g = null;
        $h = null;
        $i = null;
        $j = null;
        $k = null;
        $l = null;
        $m = null;
        $n = null;
        $o = null;
        $p = null;
        $result = $this->data['complex']->sort();

        foreach ( range( 'a', 'p' ) as $letter )
            ${$letter} = array_search( $letter, $result );

        static::assertSame(16, is_countable($result) ? count( $result ) : 0);
        static::assertLessThan($c, $b);
        static::assertLessThan($b, $a);
        static::assertLessThan($d, $b);
        static::assertLessThan($e, $d);
        static::assertLessThan($e, $c);
        static::assertLessThan($f, $a);
        static::assertLessThan($f, $b);
        static::assertLessThan($f, $c);
        static::assertLessThan($f, $d);
        static::assertLessThan($f, $e);
        static::assertLessThan($g, $h);
        static::assertLessThan($g, $i);
        static::assertLessThan($g, $j);
        static::assertLessThan($g, $k);
        static::assertLessThan($k, $j);
        static::assertLessThan($j, $e);
        static::assertLessThan($l, $m);
        static::assertLessThan($m, $n);
        static::assertLessThan($o, $p);
        static::assertLessThan($p, $g);
    }

    public function testKeepOrderSort()
    {
        static::assertSame(range( 'a', 'z' ), $this->data['keep-order']->sort());
    }

    public function testEmptySort()
    {
        static::assertSame([], $this->data['empty']->sort());
    }

    public function testCycleSort()
    {
        static::assertFalse($this->data['cycle']->sort());
    }
}
?>
