<?php

namespace Nylas\Exceptions;

/**
 * ----------------------------------------------------------------------------------
 * Gone
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2020/04/26
 */
class GoneException extends NylasException
{
    protected $code = 410;

    protected $message = 'The requested resource has been removed from our servers.';
}
