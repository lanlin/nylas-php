<?php namespace Nylas\Utilities;

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
 * @change 2020/04/26
 */
class Options
{

    // ------------------------------------------------------------------------------

    /**
     * @var bool
     */
    private bool $debug = false;

    /**
     * @var bool
     */
    private bool $offDecodeError = false;

    /**
     * @var string
     */
    private string $logFile;

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
            V::key('log_file', V::stringType()->notEmpty(), false),
            V::key('account_id', V::stringType()->notEmpty(), false),
            V::key('access_token', V::stringType()->notEmpty(), false),
            V::key('off_decode_error', V::boolType(), false),

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
        $this->setOffDecodeError($options['off_decode_error'] ?? false);
    }

    // ------------------------------------------------------------------------------

    /**
     * set access token
     *
     * @param string $token
     */
    public function setAccessToken(string $token) : void
    {
        $this->accessToken = $token;

        if (!$token) { return; }

        // cache account info
        $this->accountInfo = (new Account($this))->getAccount();
    }

    // ------------------------------------------------------------------------------

    /**
     * get access token
     *
     * @return string
     */
    public function getAccessToken() : ?string
    {
        return $this->accessToken ?? null;
    }

    // ------------------------------------------------------------------------------

    /**
     * set account id
     *
     * @param string $id
     */
    public function setAccountId(string $id) : void
    {
        $this->accountId = $id;
    }

    // ------------------------------------------------------------------------------

    /**
     * get account id
     *
     * @return string
     */
    public function getAccountId() : ?string
    {
        return $this->accountId ?? null;
    }

    // ------------------------------------------------------------------------------

    /**
     * get server
     *
     * @return string
     */
    public function getServer() : string
    {
        return API::LIST['server'];
    }

    // ------------------------------------------------------------------------------

    /**
     * enable/disable debug
     *
     * @param bool $debug
     */
    public function setDebug(bool $debug) : void
    {
        $this->debug = $debug;
    }

    // ------------------------------------------------------------------------------

    /**
     * set log file
     *
     * @param string $logFile
     */
    public function setLogFile(string $logFile) : void
    {
        $this->logFile = $logFile;
    }

    // ------------------------------------------------------------------------------

    /**
     * enable/disable decode error (true => close, false => open)
     *
     * @param bool $off
     */
    public function setOffDecodeError(bool $off) : void
    {
        $this->offDecodeError = $off;
    }

    // ------------------------------------------------------------------------------

    /**
     * get decode error set
     */
    public function getOffDecodeError() : bool
    {
        return $this->offDecodeError;
    }

    // ------------------------------------------------------------------------------

    /**
     * set client id & secret
     *
     * @param string $clientId
     * @param string $clientSecret
     */
    public function setClientApps(string $clientId, string $clientSecret) : void
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
    public function getClientApps() : array
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
    public function getAllOptions() : array
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
            'off_decode_error' => $this->offDecodeError,
        ];
    }

    // ------------------------------------------------------------------------------

    /**
     * get sync request instance
     *
     * @return \Nylas\Request\Sync
     */
    public function getSync() : Sync
    {
        $debug  = $this->debug;
        $server = $this->getServer();

        // when set log file
        if ($this->debug && !empty($this->logFile))
        {
            $debug = fopen($this->logFile, 'ab');
        }

        return new Sync($server, $debug, $this->offDecodeError);
    }

    // ------------------------------------------------------------------------------

    /**
     * get async request instance
     *
     * @return \Nylas\Request\Async
     */
    public function getAsync() : Async
    {
        $debug  = $this->debug;
        $server = $this->getServer();

        // when set log file
        if ($this->debug && !empty($this->logFile))
        {
            $debug = fopen($this->logFile, 'ab');
        }

        return new Async($server, $debug, $this->offDecodeError);
    }

    // ------------------------------------------------------------------------------

    /**
     * get account infos
     *
     * @return array
     */
    public function getAccount() : array
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

        return array_merge($temp, $this->accountInfo);
    }

    // ------------------------------------------------------------------------------

}
