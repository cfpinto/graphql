<?php


namespace GraphQL\Contracts\Properties;


interface IsQueryableInterface
{
    public function query(int $index = 0, bool $prettify = true): string;

    public function toQL(int $index = 0, bool $prettify = true): string;
}
