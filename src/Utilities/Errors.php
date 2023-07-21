<?php

declare(strict_types = 1);

namespace Nylas\Utilities;

use Nylas\Exceptions;

/**
 * ----------------------------------------------------------------------------------
 * Nylas RESTFul Request Errors
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2023/07/21
 */
class Errors
{
    // ------------------------------------------------------------------------------

    /**
     * everything is ok
     */
    public const StatusOK = 200;

    // ------------------------------------------------------------------------------

    /**
     * http status code to exceptions
     */
    public const StatusExceptions = [
        202 => Exceptions\NotReadyException::class,
        400 => Exceptions\BadRequestException::class,
        401 => Exceptions\UnauthorizedException::class,
        402 => Exceptions\RequestFailedException::class,
        403 => Exceptions\ForbiddenException::class,
        404 => Exceptions\NotFoundException::class,
        405 => Exceptions\MethodNotAllowedException::class,
        410 => Exceptions\GoneException::class,
        418 => Exceptions\TeapotException::class,
        422 => Exceptions\SendingErrorException::class,
        429 => Exceptions\TooManyRequestsException::class,
        500 => Exceptions\ServerErrorException::class,
        502 => Exceptions\ServerErrorException::class,
        503 => Exceptions\ServerErrorException::class,
        504 => Exceptions\ServerErrorException::class,

        'default' => Exceptions\NylasException::class,
    ];

    // ------------------------------------------------------------------------------
}
