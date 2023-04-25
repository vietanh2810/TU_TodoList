<?php

class Calculatrice
{
    public static function add(int $a, int $b)
    {
        return $a + $b;
    }

    public static function sub(int $a, int $b)
    {
        return $a - $b;
    }

    public static function mul(int $a, int$b)
    {
        return $a * $b;
    }

    public static function div(int $a, int $b)
    {
        if ($b == 0){
            throw new Exception('Cannot divide by zero.');
        }
        return $a / $b;
    }

//    public static function avg()
//    {
//        return self::div(self::add($a,$b),2);
//    }

    public static function avg($arr) {
        $sum = 0;
        $len = 0;
        foreach($arr as $value){
            $sum += $value;
            $len++;
        }

        if ($len == 0) {
            throw new Exception('Cannot calculate average of empty array.');
        }

        return self::div($sum,$len);
    }
}