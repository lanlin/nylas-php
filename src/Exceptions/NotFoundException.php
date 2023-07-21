<?php

declare(strict_types = 1);

namespace Nylas\Exceptions;

/**
 * ----------------------------------------------------------------------------------
 * Not Found
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2023/07/21
 */
class NotFoundException extends NylasException
{
    protected $code = 404;

    protected $message = "The requested item doesn't exist.";
}
