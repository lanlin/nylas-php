<?php namespace Nylas\Exceptions;

/**
 * ----------------------------------------------------------------------------------
 * Server Error
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2020/04/26
 */
class ServerErrorException extends NylasException
{
    protected $code = 500;

    protected $message = 'An error occurred in the Nylas server. If this persists, please see our status page or contact support.';
}