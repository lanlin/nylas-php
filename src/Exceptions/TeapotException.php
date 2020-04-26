<?php namespace Nylas\Exceptions;

/**
 * ----------------------------------------------------------------------------------
 * Teapot
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2020/04/26
 */
class TeapotException extends NylasException
{
    protected $code = 418;

    protected $message = "I'm a teapot";
}