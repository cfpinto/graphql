<?php


namespace GraphQL\Utils;


class Str
{
    const TAB = 4;
    const IDENT_CHAR = ' ';

    public static function ident(string $str): string
    {
        $lines = explode(PHP_EOL, $str);
        $level = 0;

        foreach ($lines as $i => $line) {
            $line = trim($line);
            if ($i > 0) {
                if (preg_match('/{$/', $lines[$i - 1])) {
                    $level++;
                }

                if (preg_match('/^}/', $line)) {
                    $level--;
                }
            }

            $lines[$i] = str_repeat(self::IDENT_CHAR, $level * self::TAB) . $line;
        }

        $str = implode(PHP_EOL, array_filter($lines, fn(string $item) => trim($item) !== ''));

        return $str;
    }

    public static function ugliffy(string $str): string
    {
        return trim(
            self::stripSpaces(self::reduceDouble(str_replace(PHP_EOL, ' ', $str)))
        );
    }

    public static function reduceDouble(string $str, string $reduce = ' '): string
    {
        return preg_replace('#' . preg_quote($reduce, '#') . '{2,}#', $reduce, $str);
    }

    public static function stripSpaces(string $str): string
    {
        return preg_replace('/(?<=({|}))(\s+)(?=})/', '', $str);
    }
}
