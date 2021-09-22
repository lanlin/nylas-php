<?php

namespace Nylas\Exceptions;

/**
 * ----------------------------------------------------------------------------------
 * Teapot
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2021/09/22
 */
class TeapotException extends NylasException
{
    protected $code = 418;

    protected $message = "I'm a teapot";
}
