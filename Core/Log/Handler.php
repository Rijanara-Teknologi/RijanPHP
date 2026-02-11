<?php

namespace Teguh02\Rijanphp\Core\Log;

interface Handler
{
    /**
     * Write a log record.
     *
     * @param string $level
     * @param string $message
     * @param array $context
     * @return void
     */
    public function write($level, $message, array $context = []);
}
