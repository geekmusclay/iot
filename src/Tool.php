<?php

declare(strict_types=1);

namespace Geekmusclay\IOT;

use function array_search;
use function bcadd;
use function bcdiv;
use function bcmod;
use function bcmul;
use function bcpow;
use function explode;
use function str_split;
use function strlen;
use function strval;

/**
 * This class is a compilation of functions useful to the package.
 */
class Tool
{
    /**
     * @see https://www.php.net/manual/fr/function.base-convert.php#106546
     *
     * Convert an arbitrarily large number from any base to any base.
     *
     * @param  string|int $numberInput    The number input
     * @param  string     $fromBaseInput  The from base input
     * @param  string     $toBaseInput    To base input
     *
     * @return string Converted number
     */
    public static function convBase($numberInput, string $fromBaseInput, string $toBaseInput): string
    {
        // phpcs:disable
        if ($fromBaseInput == $toBaseInput) {
            return $numberInput;
        }

        $fromBase = str_split($fromBaseInput, 1);
        $toBase   = str_split($toBaseInput, 1);
        $number   = str_split($numberInput, 1);

        $fromLen   = (string) strlen($fromBaseInput);
        $toLen     = (string) strlen($toBaseInput);
        $numberLen = (string) strlen($numberInput);

        $retval = '';
        if ($toBaseInput == '0123456789') {
            for ($i = 1; $i <= $numberLen; $i++) {
                $retval = bcadd(
                    $retval,
                    bcmul(
                        (string) array_search($number[$i - 1], $fromBase),
                        bcpow((string) $fromLen, strval($numberLen - $i))
                    )
                );
            }

            return $retval;
        }

        if ($fromBaseInput != '0123456789') {
            $base10 = self::convBase($numberInput, $fromBaseInput, '0123456789');
        } else {
            $base10 = $numberInput;
        }

        if ($base10 < strlen($toBaseInput)) {
            return $toBase[$base10];
        }

        while ($base10 != '0') {
            $retval = $toBase[ bcmod($base10, $toLen) ] . $retval;
            $base10 = bcdiv($base10, $toLen, 0);
        }

        return $retval;
        // phpcs:enable
    }

    /**
     * This function was designed to fixed problem of parsing Hexadecimal string like 34
     * with convBase function who give in binary "100010".
     *
     * But in case we are encoding octet, we want "00100010" as a result.
     * The error occured only when multiple 0 are at first place.
     * And this is is the purpose of this function.
     *
     * @param  string|int  $input  The input
     * @return string      Fixed input (if necessary)
     */
    public static function octetFormat($input)
    {
        $targetLength = strlen($input) * 4;

        /** @var string $result Converted input */
        $result       = self::convBase($input, '0123456789ABCDEF', '01');
        $resultLength = strlen($result);

        if ($resultLength === $targetLength) {
            return $result;
        }
        $difference = $targetLength - $resultLength;

        for ($i = 0; $i < $difference; $i++) {
            $result = '0' . $result;
        }

        return $result;
    }

    /**
     * Will search for value in array according to given path.
     * Path should look like this : level1.level2.level3
     *
     * @param  array   $array  The concerned array
     * @param  string  $path   The path to get value
     * @return mixed|null Value found in array
     */
    public static function arrayPropGet(array $array, string $path)
    {
        $parts = explode('.', $path);

        $return = $array;
        foreach ($parts as $part) {
            if (false === isset($return[$part])) {
                return null;
            }
            $return = $return[$part];
        }

        return $return;
    }

    /**
     * Will set value in array according to given path.
     * Path should look like this : level1.level2.level3
     *
     * @param  array   $array  The concerned array
     * @param  string  $path   The path where to set value
     * @param  mixed   $value  The value to set
     * @return bool    True if succeed, false if failed
     */
    public static function arrayPropSet(array &$array, string $path, $value): bool
    {
        $parts = explode('.', $path);

        $tmp = &$array;
        foreach ($parts as $part) {
            if (false === isset($tmp[$part])) {
                return false;
            }
            $tmp = &$tmp[$part];
        }
        $tmp = $value;

        return true;
    }

    /**
     * Simply check that all the specified properties are present.
     *
     * @param  array   $datas      Data table to be checked
     * @param  array   $properties Properties to check
     * @return boolean Returns true if a property is missing, false otherwise
     */
    public static function isMissingOneOf(array $datas, array $properties): bool
    {
        foreach ($properties as $property) {
            if (false === isset($datas[$property])) {
                return true;
            }
        }

        return false;
    }
}
