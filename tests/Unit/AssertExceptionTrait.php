<?php

namespace Tests\Unit;

use GraphQL\Exceptions\InvalidArgumentTypeException;

trait AssertExceptionTrait
{
    /**
     * @param Callable $func
     * @return void
     */
    private function assertThrowsException(callable $func, string $type = null, string $msg = null)
    {
        try {
            $func();
            $this->fail('no exception thrown');
        } catch (\Throwable $e) {
            if ($type && get_class($e) !== $type) {
                $this->fail(sprintf('invalid exception type, expecting %s but got %s', $type, get_class($e)));
            }

            if ($msg) {
                $this->assertEquals($msg, $e->getMessage());
            }
        }
    }
}
