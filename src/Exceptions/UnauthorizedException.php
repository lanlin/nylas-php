<?php namespace Nylas\Exceptions;

/**
 * ----------------------------------------------------------------------------------
 * Unauthorized
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2020/04/26
 */
class UnauthorizedException extends NylasException
{
    protected $code = 401;

    protected $message = 'No valid API key or access_token provided.';
}