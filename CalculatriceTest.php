<?php

require_once './Calculatrice.php';

use PHPUnit\Framework\TestCase;

class CalculatriceTest extends TestCase {
    public function testAdd() {
        $result = Calculatrice::add(2, 3);
        $this->assertEquals(5, $result);
    }

    public function testSub() {
        $result = Calculatrice::sub(4, 1);
        $this->assertEquals(3, $result);
    }

    public function testMul() {
        $result = Calculatrice::mul(5, 6);
        $this->assertEquals(30, $result);
    }

    public function testDiv() {
        $result = Calculatrice::div(10, 2);
        $this->assertEquals(5, $result);

        $this->expectException(Exception::class);
        Calculatrice::div(10, 0);
    }

    public function testAvg() {
        $result = Calculatrice::avg([7, 9]);
        $this->assertEquals(8, $result);

        $result = Calculatrice::avg([5, 5, 6, 7, 2]);
        $this->assertEquals(5, $result);

        $this->expectException(Exception::class);
        Calculatrice::avg([]);

        $result = Calculatrice::avg([7]);
        $this->assertEquals(7, $result);

        $numbers = [-2, -4, -6, -8, -10];
        $result = Calculatrice::avg($numbers);
        $this->assertEquals(-6, $result);

        $numbers = [10, 20, 30, 40, 50];
        $result = Calculatrice::avg($numbers);
        $this->assertEquals(30, $result);

    }
}
