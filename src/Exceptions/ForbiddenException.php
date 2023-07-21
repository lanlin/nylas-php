<?php

declare(strict_types = 1);

namespace Nylas\Exceptions;

/**
 * ----------------------------------------------------------------------------------
 * Forbidden
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2023/07/21
 */
class ForbiddenException extends NylasException
{
    protected $code = 403;

    protected $message = 'Includes authentication errors, blocked developer applications, and cancelled accounts.';
}
