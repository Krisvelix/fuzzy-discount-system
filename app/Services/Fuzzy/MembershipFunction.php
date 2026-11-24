<?php

namespace App\Services\Fuzzy;

class MembershipFunction
{
    /**
     * Fungsi keanggotaan segitiga.
     */
    public static function triangle(
        float $x,
        float $a,
        float $b,
        float $c
    ): float {
        if ($x <= $a || $x >= $c) {
            return 0.0;
        }

        if ($x == $b) {
            return 1.0;
        }

        if ($x < $b) {
            return ($x - $a) / ($b - $a);
        }

        return ($c - $x) / ($c - $b);
    }

    /**
     * Fungsi keanggotaan trapesium.
     */
    public static function trapezoid(
        float $x,
        float $a,
        float $b,
        float $c,
        float $d
    ): float {
        if ($x < $a || $x > $d) {
            return 0.0;
        }

        if ($x >= $b && $x <= $c) {
            return 1.0;
        }

        if ($x < $b) {
            return ($x - $a) / ($b - $a);
        }

        return ($d - $x) / ($d - $c);
    }
}
