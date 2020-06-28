<?php

namespace Nylas\Utilities;

use Nylas\Request\Sync;
use Nylas\Request\Async;
use Nylas\Accounts\Account;
use Nylas\Utilities\Validator as V;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Utils Options
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2020/06/18
 */
class Options
{
    // ------------------------------------------------------------------------------

    /**
     * @var mixed
     */
    private $logFile;

    /**
     * @var bool
     */
    private bool $debug = false;

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
     * @var string
     */
    private string $accountId;

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
            V::key('debug', V::boolType(), false),
            V::key('log_file', $this->getLogFileRule(), false),
            V::key('account_id', V::stringType()->notEmpty(), false),
            V::key('access_token', V::stringType()->notEmpty(), false),
            V::key('client_id', V::stringType()->notEmpty()),
            V::key('client_secret', V::stringType()->notEmpty())
        );

        V::doValidate($rules, $options);

        // required
        $this->setClientApps($options['client_id'], $options['client_secret']);

        // optional
        $this->setDebug($options['debug'] ?? false);
        $this->setLogFile($options['log_file'] ?? null);
        $this->setAccountId($options['account_id'] ?? '');
        $this->setAccessToken($options['access_token'] ?? '');
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
    public function getAccessToken(): ?string
    {
        return $this->accessToken ?? null;
    }

    // ------------------------------------------------------------------------------

    /**
     * set account id
     *
     * @param string $id
     */
    public function setAccountId(string $id): void
    {
        $this->accountId = $id;
    }

    // ------------------------------------------------------------------------------

    /**
     * get account id
     *
     * @return string
     */
    public function getAccountId(): ?string
    {
        return $this->accountId ?? null;
    }

    // ------------------------------------------------------------------------------

    /**
     * get server
     *
     * @return string
     */
    public function getServer(): string
    {
        return API::LIST['server'];
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
    public function setLogFile($logFile): void
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
     * get all configure options
     *
     * @return array
     */
    public function getAllOptions(): array
    {
        return
        [
            'debug'            => $this->debug,
            'log_file'         => $this->logFile,
            'server'           => API::LIST['server'],
            'client_id'        => $this->clientId,
            'client_secret'    => $this->clientSecret,
            'account_id'       => $this->accountId,
            'access_token'     => $this->accessToken,
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
        $server = $this->getServer();

        $debug = $this->getLoggerHandler();

        return new Sync($server, $debug);
    }

    // ------------------------------------------------------------------------------

    /**
     * get async request instance
     *
     * @return \Nylas\Request\Async
     */
    public function getAsync(): Async
    {
        $server = $this->getServer();

        $debug = $this->getLoggerHandler();

        return new Async($server, $debug);
    }

    // ------------------------------------------------------------------------------

    /**
     * get account infos
     *
     * @return array
     */
    public function getAccount(): array
    {
        $temp =
        [
            'id'                => '',
            'account_id'        => '',
            'email_address'     => '',
            'name'              => '',
            'object'            => '',
            'provider'          => '',
            'linked_at'         => null,
            'sync_state'        => '',
            'organization_unit' => '',
        ];

        if (empty($this->accountInfo) && !empty($this->accessToken))
        {
            $this->accountInfo = (new Account($this))->getAccount();
        }

        return \array_merge($temp, $this->accountInfo);
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
    private function getLoggerHandler()
    {
        switch (true)
        {
            case \is_string($this->logFile):
            return \fopen($this->logFile, 'ab');

            case \is_resource($this->logFile):
            return $this->logFile;

            default:
            return $this->debug;
        }
    }

    // ------------------------------------------------------------------------------
}
