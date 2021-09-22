<?php

namespace Nylas\Exceptions;

/**
 * ----------------------------------------------------------------------------------
 * Not Found
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2021/09/22
 */
class NotFoundException extends NylasException
{
    protected $code = 404;

    protected $message = "The requested item doesn't exist.";
}
