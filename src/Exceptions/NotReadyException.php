<?php

declare(strict_types = 1);

namespace Nylas\Exceptions;

/**
 * ----------------------------------------------------------------------------------
 * Not Ready
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2023/07/21
 */
class NotReadyException extends NylasException
{
    protected $code = 202;

    protected $message = "The request was valid but the resource wasn't ready. Retry the request with exponential backoff";
}
