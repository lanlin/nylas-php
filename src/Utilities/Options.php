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
 * @change 2021/07/27
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
        $rules = V::keySet(
            V::key('client_id', V::stringType()->notEmpty()),
            V::key('client_secret', V::stringType()->notEmpty()),
            V::keyOptional('debug', V::boolType()),
            V::keyOptional('region', V::in(['us', 'canada', 'ireland'])),
            V::keyOptional('handler', V::callableType()),
            V::keyOptional('log_file', $this->getLogFileRule()),
            V::keyOptional('access_token', V::stringType()->notEmpty()),
        );

        V::doValidate($rules, $options);

        $this->setDebug($options['debug'] ?? false);
        $this->setServer($options['region'] ?? 'us');
        $this->setHandler($options['handler'] ?? null);
        $this->setLogFile($options['log_file'] ?? null);
        $this->setAccessToken($options['access_token'] ?? '');
        $this->setClientApps($options['client_id'], $options['client_secret']);
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
     * set client id & secret
     *
     * @param string $clientId
     * @param string $clientSecret
     */
    public function setClientApps(string $clientId, string $clientSecret): void
    {
        $this->clientId     = $clientId;
        $this->clientSecret = $clientSecret;
    }

    // ------------------------------------------------------------------------------

    /**
     * get client id & secret
     *
     * @return array
     */
    public function getClientApps(): array
    {
        return
        [
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
        ];
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
            $this->accountInfo = (new Account($this))->getAccountDetail();
        }

        return $this->accountInfo;
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
