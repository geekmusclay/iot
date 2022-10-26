<?php

declare(strict_types=1);

namespace Geekmusclay\IOT;

use Geekmusclay\IOT\Tool;
use InvalidArgumentException;

use function count;
use function strtoupper;
use function substr;

class Decoder
{
    public static function decode(string $frame, array $config): array
    {
        $result = [
            'frame' => $frame,
        ];
        foreach ($config as $detail) {
            if (true === Tool::isMissingOneOf($detail, ['from', 'to', 'origin', 'target', 'name'])) {
                throw new InvalidArgumentException('Properties are missing in config');
            }

            $sub = substr($frame, $detail['from'], $detail['to']);
            $sub = strtoupper($sub);

            if ($detail['origin'] === '0123456789ABCDEF' && $detail['target'] === '01') {
                $result[ $detail['name'] ] = Tool::octetFormat($sub);
            } else {
                $result[ $detail['name'] ] = Tool::convBase($sub, $detail['origin'], $detail['target']);
            }

            if (true === isset($detail['subdetails']) && 0 !== count($detail['subdetails'])) {
                foreach ($detail['subdetails'] as $subdetail) {
                    if (true === Tool::isMissingOneOf($detail, ['from', 'to', 'target', 'name'])) {
                        throw new InvalidArgumentException('Properties are missing in subdetails config');
                    }

                    $sub = substr(
                        $result[ $detail['name'] ],
                        $subdetail['from'],
                        $subdetail['to']
                    );
                    $sub = strtoupper($sub);

                    $result['subdetails'][ $subdetail['name']] = Tool::convBase(
                        $sub,
                        $detail['target'],
                        $subdetail['target']
                    );
                }
            }
        }

        return $result;
    }
}
