<?php namespace Nylas\Exceptions;

/**
 * ----------------------------------------------------------------------------------
 * Sending Error
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/11/21
 */
class SendingErrorException extends NylasException
{
    protected $code = 422;

    protected $message = 'This is returned during sending. See sending errors';
}