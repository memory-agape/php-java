<?php
namespace PHPJava\Utilities;

class Formatter
{
    public static function parseSignature($signature, $i = 0)
    {
        $getMappedSignatureType = function ($signature) {
            switch ($signature) {
                case 'B': return 'byte';
                case 'C': return 'char';
                case 'D': return 'double';
                case 'F': return 'float';
                case 'I': return 'int';
                case 'J': return 'long';
                case 'S': return 'short';
                case 'V': return 'void';
                case 'Z': return 'boolean';
            }
            return 'Undefined';
        };
        $data = [];
        $deepArray = 0;

        for ($size = strlen($signature); $i < $size; ) {
            switch ($signature[$i]) {
                case 'B':
                case 'C':
                case 'D':
                case 'F':
                case 'I':
                case 'J':
                case 'S':
                case 'V':
                case 'Z':
                    $data[] = [
                        'type' => $getMappedSignatureType($signature[$i]),
                        'deepArray' => $deepArray,
                    ];
                    $deepArray = 0;
                    break;
                case 'L':
                    // class name
                    $build = '';
                    // read to ;
                    for ($i++; $i < $size && $signature[$i] !== ';'; $i++) {
                        $build .= $signature[$i];

                    }
                    $data[] = [
                        'type' => 'class',
                        'deepArray' => $deepArray,
                        'className' => $build,
                    ];
                    $deepArray = 0;

                    break;
                case '[':
                    // array
                    $deepArray++;
                    for ($i++; $signature[$i] === '['; $i++) {
                        $deepArray++;
                    }
                    // loop
                    continue 2;
                case '(':
                    $build = '';
                    // read to )
                    for ($i++; $i < $size && $signature[$i] !== ')'; $i++) {
                        $build .= $signature[$i];
                    }
                    $data['arguments'] = ($build !== '') ? $getMappedSignatureType($build) : [];
                    $data['argumentsCount'] = sizeof($data['arguments']);
                    break;
            }
            $i++;
        }
        return $data;
    }
}