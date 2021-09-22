<?php

namespace Nylas\Exceptions;

use Throwable;
use RuntimeException;

/**
 * ----------------------------------------------------------------------------------
 * NylasException
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2021/09/22
 */
class NylasException extends RuntimeException
{
    // ------------------------------------------------------------------------------

    protected $code = 999;

    protected $message = 'some issue found when calling nylas.';

    // ------------------------------------------------------------------------------

    /**
     * NylasException constructor.
     *
     * @param null|Throwable $exception
     * @param string         $message
     * @param int            $code
     */
    public function __construct(?Throwable $exception = null, string $message = '', int $code = 0)
    {
        $this->checkIfHasNylasException($exception);

        $msgs = $exception ? $exception->getMessage() : $this->message;
        $msgs = $message ?: $msgs;
        $code = $code ?: $this->code;

        parent::__construct($msgs, $code, $exception);
    }

    // ------------------------------------------------------------------------------

    /**
     * check if has nylas exception throwed
     *
     * @param null|Throwable $exception
     *
     * @throws Throwable
     */
    private function checkIfHasNylasException(?Throwable $exception = null): void
    {
        if ($exception instanceof NylasException)
        {
            throw $exception;
        }

        if ($exception && $exception->getPrevious())
        {
            $this->checkIfHasNylasException($exception->getPrevious());
        }
    }

    // ------------------------------------------------------------------------------
}
