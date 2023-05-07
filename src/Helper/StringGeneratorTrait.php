<?php

namespace App\Helper;

Trait StringGeneratorTrait
{
    /**
     * Generates a pseudo-random password of the specified length.
     * @param int $length How long the password will be.
     * * MIN = 12
     * * MAX = 48
     * @param bool $specialChars Sets if the password will contain non-alphanumeric characters.
     * @return string The password.
     */
    public static function generateRandomPassword(?int $length = 12, bool $special = false)
    {
        $length < 12 ? $length = 12 : $length;
        $length > 48 ? $length = 48 : $length;
        $pool = [
            ["a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z"],
            ["A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z"],
            ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"]
        ];
        $specialPool = [";", "!", ":", "?", "-", ",", "(", ")", "{", "}", "+", ".", "#", "_", "*", "%", "^", "+", "@", "&", "[", "]"];
        if ($special) {
            $pool[] = $specialPool;
        }
        $password = "";
        for ($i = 0; $i < $length; $i++) {
            $poolIndex = rand(0, count($pool) - 1);
            $charIndex = rand(0, count($pool[$poolIndex]) - 1);
            $password .= $pool[$poolIndex][$charIndex];
        }

        return str_shuffle($password);
    }
}
