<?php namespace Nylas\Exceptions;

/**
 * ----------------------------------------------------------------------------------
 * Teapot
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/11/21
 */
class TeapotException extends NylasException
{
    protected $code = 418;

    protected $message = "I'm a teapot";
}