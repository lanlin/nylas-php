<?php namespace Nylas\Exceptions;

/**
 * ----------------------------------------------------------------------------------
 * Bad Request
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/11/21
 */
class BadRequestException extends NylasException
{
    protected $code = 400;

    protected $message = 'Malformed or missing a required parameter.';
}