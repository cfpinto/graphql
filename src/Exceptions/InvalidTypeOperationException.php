<?php


namespace GraphQL\Exceptions;


use Throwable;

class InvalidTypeOperationException extends InvalidArgumentTypeException
{
    public function __construct(string $used, string $expected, int $code = 0, Throwable $previous = null)
    {
        $this->template = sprintf('Invalid type %%s expected type %s', $expected);

        parent::__construct($used, $code, $previous);
    }
}
