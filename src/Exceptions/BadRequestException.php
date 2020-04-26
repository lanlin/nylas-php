<?php namespace Nylas\Exceptions;

/**
 * ----------------------------------------------------------------------------------
 * Bad Request
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2020/04/26
 */
class BadRequestException extends NylasException
{
    protected $code = 400;

    protected $message = 'Malformed or missing a required parameter.';
}