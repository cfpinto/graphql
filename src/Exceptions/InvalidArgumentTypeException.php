<?php


namespace GraphQL\Exceptions;


use Throwable;

class InvalidArgumentTypeException extends \Exception
{
    protected string $type;

    protected string $template = 'Invalid argument type %s';

    public function __construct(string $type, int $code = 0, Throwable $previous = null)
    {
        $this->type = $type;

        parent::__construct(sprintf($this->template, $this->type), $code, $previous);
    }
}
