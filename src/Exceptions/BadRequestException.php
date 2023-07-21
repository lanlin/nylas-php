<?php

declare(strict_types = 1);

namespace Nylas\Exceptions;

/**
 * ----------------------------------------------------------------------------------
 * Bad Request
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2023/07/21
 */
class BadRequestException extends NylasException
{
    protected $code = 400;

    protected $message = 'Malformed or missing a required parameter, or your email provider not support this.';
}
