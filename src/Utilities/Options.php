<?php

namespace Nylas\Utilities;

use Nylas\Request\Sync;
use Nylas\Request\Async;
use Nylas\Management\Account;
use Nylas\Utilities\Validator as V;
use Nylas\Exceptions\UnauthorizedException;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Utils Options
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2021/09/22
 */
class Options
{
    // ------------------------------------------------------------------------------

    /**
     * @var mixed
     */
    private mixed $logFile;

    /**
     * @var null|callable
     */
    private mixed $handler;

    /**
     * @var bool
     */
    private bool $debug = false;

    /**
     * @var string
     */
    private string $server;

    /**
     * @var string
     */
    private string $clientId;

    /**
     * @var string
     */
    private string $clientSecret;

    /**
     * @var string
     */
    private string $accessToken;

    /**
     * @var array
     */
    private array $accountInfo;

    // ------------------------------------------------------------------------------

    /**
     * Options constructor.
     *
     * @param array $options
     */
    public function __construct(array $options)
    {
        V::doValidate(V::keySet(
            V::key('client_id', V::stringType()->notEmpty()),
            V::key('client_secret', V::stringType()->notEmpty()),
            V::keyOptional('debug', V::boolType()),
            V::keyOptional('region', V::in(['us', 'canada', 'ireland'])),
            V::keyOptional('handler', V::callableType()),
            V::keyOptional('log_file', $this->getLogFileRule()),
            V::keyOptional('access_token', V::stringType()->notEmpty()),
        ), $options);

        $this->setClientId($options['client_id']);
        $this->setClientSecret($options['client_secret']);

        $this->setDebug($options['debug'] ?? false);
        $this->setServer($options['region'] ?? 'us');
        $this->setHandler($options['handler'] ?? null);
        $this->setLogFile($options['log_file'] ?? null);
        $this->setAccessToken($options['access_token'] ?? '');
    }

    // ------------------------------------------------------------------------------

    /**
     * set guzzle client handler
     *
     * @param null|callable $handler
     */
    public function setHandler(?callable $handler): void
    {
        $this->handler = $handler;
    }

    // ------------------------------------------------------------------------------

    /**
     * get access token
     *
     * @return string
     */
    public function getHandler(): ?callable
    {
        return $this->handler ?? null;
    }

    // ------------------------------------------------------------------------------

    /**
     * @param null|string $region
     */
    public function setServer(?string $region = null): void
    {
        $region = $region ?? 'us';

        $this->server = API::SERVER[$region] ?? API::SERVER['us'];
    }

    // ------------------------------------------------------------------------------

    /**
     * get server
     *
     * @return string
     */
    public function getServer(): string
    {
        return $this->server;
    }

    // ------------------------------------------------------------------------------

    /**
     * enable/disable debug
     *
     * @param bool $debug
     */
    public function setDebug(bool $debug): void
    {
        $this->debug = $debug;
    }

    // ------------------------------------------------------------------------------

    /**
     * set log file
     *
     * @param mixed $logFile
     */
    public function setLogFile(mixed $logFile): void
    {
        if (null !== $logFile)
        {
            V::doValidate($this->getLogFileRule(), $logFile);
        }

        $this->logFile = $logFile;
    }

    // ------------------------------------------------------------------------------

    /**
     * set client id
     *
     * @param string $clientId
     */
    public function setClientId(string $clientId): void
    {
        $this->clientId = $clientId;
    }

    // ------------------------------------------------------------------------------

    /**
     * set client secret
     *
     * @param string $clientSecret
     */
    public function setClientSecret(string $clientSecret): void
    {
        $this->clientSecret = $clientSecret;
    }

    // ------------------------------------------------------------------------------

    /**
     * get client id
     *
     * @return string
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    // ------------------------------------------------------------------------------

    /**
     * get client secret
     *
     * @return string
     */
    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    // ------------------------------------------------------------------------------

    /**
     * set access token
     *
     * @param string $token
     */
    public function setAccessToken(string $token): void
    {
        $this->accessToken = $token;

        if (!$token)
        {
            return;
        }

        $this->accountInfo = [];
    }

    // ------------------------------------------------------------------------------

    /**
     * get access token
     *
     * @return string
     */
    public function getAccessToken(): string
    {
        if (!$this->accessToken)
        {
            throw new UnauthorizedException();
        }

        return $this->accessToken;
    }

    // ------------------------------------------------------------------------------

    /**
     * get authorization header
     *
     * @param bool $isAccessToken
     * @return array
     */
    public function getAuthorizationHeader(bool $isAccessToken = true): array
    {
        $authorization = $isAccessToken ? $this->getAccessToken() : $this->clientSecret;

        return ['Authorization' => $authorization];
    }

    // ------------------------------------------------------------------------------

    /**
     * get all configure options
     *
     * @return array
     */
    public function getAllOptions(): array
    {
        return
        [
            'debug'         => $this->debug,
            'log_file'      => $this->logFile,
            'server'        => $this->server,
            'handler'       => $this->handler,
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'access_token'  => $this->accessToken,
        ];
    }

    // ------------------------------------------------------------------------------

    /**
     * get sync request instance
     *
     * @return \Nylas\Request\Sync
     */
    public function getSync(): Sync
    {
        $debug   = $this->getLoggerHandler();
        $server  = $this->getServer();
        $handler = $this->getHandler();

        return new Sync($server, $handler, $debug);
    }

    // ------------------------------------------------------------------------------

    /**
     * get async request instance
     *
     * @return \Nylas\Request\Async
     */
    public function getAsync(): Async
    {
        $debug   = $this->getLoggerHandler();
        $server  = $this->getServer();
        $handler = $this->getHandler();

        return new Async($server, $handler, $debug);
    }

    // ------------------------------------------------------------------------------

    /**
     * get account infos
     *
     * @return array
     */
    public function getAccount(): array
    {
        if (empty($this->accountInfo))
        {
            $this->accountInfo = (new Account($this))->returnAccountDetails();
        }

        return $this->accountInfo;
    }

    // ------------------------------------------------------------------------------

    /**
     * get account id
     *
     * @return string
     */
    public function getAccountId(): string
    {
        $account = $this->getAccount();

        if (empty($account))
        {
            throw new UnauthorizedException();
        }

        return $this->accessToken;
    }

    // ------------------------------------------------------------------------------

    /**
     * get log file rules
     *
     * @return \Nylas\Utilities\Validator
     */
    private function getLogFileRule(): V
    {
        return V::oneOf(
            V::resourceType(),
            V::stringType()->notEmpty()
        );
    }

    // ------------------------------------------------------------------------------

    /**
     * get logger handler
     *
     * @return mixed
     */
    private function getLoggerHandler(): mixed
    {
        return match (true)
        {
            \is_string($this->logFile)   => \fopen($this->logFile, 'ab'),
            \is_resource($this->logFile) => $this->logFile,

            default => $this->debug,
        };
    }

    // ------------------------------------------------------------------------------
}
