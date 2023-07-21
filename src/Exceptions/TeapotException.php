<?php

declare(strict_types = 1);

namespace Nylas\Exceptions;

/**
 * ----------------------------------------------------------------------------------
 * Teapot
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2023/07/21
 */
class TeapotException extends NylasException
{
    protected $code = 418;

    protected $message = "I'm a teapot";
}
