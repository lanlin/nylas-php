<?php namespace Nylas\Exceptions;

/**
 * ----------------------------------------------------------------------------------
 * Method Not Allowed
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/11/21
 */
class MethodNotAllowedException extends NylasException
{
    protected $code = 405;

    protected $message = 'You tried to access a resource with an invalid method.';
}