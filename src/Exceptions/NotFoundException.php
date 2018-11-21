<?php namespace Nylas\Exceptions;

/**
 * ----------------------------------------------------------------------------------
 * Not Found
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/11/21
 */
class NotFoundException extends NylasException
{
    protected $code = 404;

    protected $message = "The requested item doesn't exist.";
}