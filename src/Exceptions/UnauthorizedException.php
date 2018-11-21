<?php namespace Nylas\Exceptions;

/**
 * ----------------------------------------------------------------------------------
 * Unauthorized
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/11/21
 */
class UnauthorizedException extends NylasException
{
    protected $code = 401;

    protected $message = 'No valid API key or access_token provided.';
}