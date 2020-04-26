<?php namespace Nylas\Exceptions;

/**
 * ----------------------------------------------------------------------------------
 * Sending Error
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2020/04/26
 */
class SendingErrorException extends NylasException
{
    protected $code = 422;

    protected $message = 'This is returned during sending. See sending errors';
}