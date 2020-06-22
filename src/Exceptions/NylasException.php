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
 * @change 2020/06/22
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
     * @param null|Throwable $previous
     * @param string         $message
     * @param int            $code
     */
    public function __construct(?Throwable $previous = null, string $message = '', int $code = 0)
    {
        if ($previous instanceof NylasException)
        {
            throw $previous;
        }

        if ($previous && ($previous->getPrevious() instanceof NylasException))
        {
            throw $previous;
        }

        $msgs = $previous ? $previous->getMessage() : $this->message;
        $msgs = $message ?: $msgs;
        $code = $code ?: $this->code;

        parent::__construct($msgs, $code, $previous);
    }

    // ------------------------------------------------------------------------------
}
