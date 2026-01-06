<?php

namespace Ympact\Wizard\Traits;

trait MarkerTrait
{
    // method to convert number to letter (1 => A)
    public function numberToLetter($number)
    {
        $letter = '';
        while ($number > 0) {
            $mod = ($number - 1) % 26;
            $letter = chr(65 + $mod).$letter; // 65 is ASCII for 'A'
            $number = (int) (($number - $mod) / 26);
        }

        return $letter;
    }

    // method to convert number to roman numeral (1 => I)
    public function numberToRoman($number)
    {
        $map = [
            1000 => 'M',
            900 => 'CM',
            500 => 'D',
            400 => 'CD',
            100 => 'C',
            90 => 'XC',
            50 => 'L',
            40 => 'XL',
            10 => 'X',
            9 => 'IX',
            5 => 'V',
            4 => 'IV',
            1 => 'I',
        ];
        $roman = '';
        foreach ($map as $value => $symbol) {
            while ($number >= $value) {
                $roman .= $symbol;
                $number -= $value;
            }
        }

        return $roman;
    }
}
