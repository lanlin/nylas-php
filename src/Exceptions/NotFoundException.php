<?php namespace Nylas\Exceptions;

/**
 * ----------------------------------------------------------------------------------
 * Not Found
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2020/04/26
 */
class NotFoundException extends NylasException
{
    protected $code = 404;

    protected $message = "The requested item doesn't exist.";
}