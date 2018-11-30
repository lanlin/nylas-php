<?php namespace Nylas\Utilities;

use Nylas\Request\Sync;
use Nylas\Request\Async;
use Nylas\Utilities\Validate as V;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Utils Options
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/11/26
 */
class Options
{

    // ------------------------------------------------------------------------------

    /**
     * @var bool
     */
    private $debug = false;

    /**
     * @var string
     */
    private $logFile;

    /**
     * @var string
     */
    private $clientId;

    /**
     * @var string
     */
    private $clientSecret;

    /**
     * @var string
     */
    private $accessToken;

    /**
     * @var string
     */
    private $accountId;

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

            V::key('client_id', V::stringType()->notEmpty()),
            V::key('client_secret', V::stringType()->notEmpty())
        );

        V::doValidate($rules, $options);

        // required
        $this->clientId     = $options['client_id'];
        $this->clientSecret = $options['client_secret'];

        // optional
        $this->debug       = $options['debug'] ?? false;
        $this->logFile     = $options['log_file'] ?? null;
        $this->accountId   = $options['account_id'] ?? '';
        $this->accessToken = $options['access_token'] ?? '';
    }

    // ------------------------------------------------------------------------------

    /**
     * @param string $token
     */
    public function setAccessToken(string $token)
    {
        $this->accessToken = $token;
    }

    // ------------------------------------------------------------------------------

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken ?? null;
    }

    // ------------------------------------------------------------------------------

    /**
     * @param string $id
     */
    public function setAccountId(string $id)
    {
        $this->accountId = $id;
    }

    // ------------------------------------------------------------------------------

    /**
     * @return string
     */
    public function getAccountId()
    {
        return $this->accountId ?? null;
    }

    // ------------------------------------------------------------------------------

    /**
     * @return string
     */
    public function getServer()
    {
        return API::LIST['server'];
    }

    // ------------------------------------------------------------------------------

    /**
     * @param bool $debug
     */
    public function setDebug(bool $debug)
    {
        $this->debug = $debug;
    }

    // ------------------------------------------------------------------------------

    /**
     * @param string $logFile
     */
    public function setLogFile(string $logFile)
    {
        $this->logFile = $logFile;
    }

    // ------------------------------------------------------------------------------

    /**
     * @param string $clientId
     * @param string $clientSecret
     */
    public function setClientApps(string $clientId, string $clientSecret)
    {
        $this->clientId     = $clientId;
        $this->clientSecret = $clientSecret;
    }

    // ------------------------------------------------------------------------------

    /**
     * @return array
     */
    public function getClientApps()
    {
        return
        [
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
        ];
    }

    // ------------------------------------------------------------------------------

    /**
     * @return array
     */
    public function getAllOptions()
    {
        return
        [
            'debug'         => $this->debug,
            'log_file'      => $this->logFile,
            'server'        => API::LIST['server'],
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'account_id'    => $this->accountId,
            'access_token'  => $this->accessToken,
        ];
    }

    // ------------------------------------------------------------------------------

    /**
     * get sync request instance
     */
    public function getSync()
    {
        $debug  = $this->debug;
        $server = $this->getServer();

        // when set log file
        if ($this->debug && !empty($this->logFile))
        {
            $debug = fopen($this->logFile, 'a');
        }

        return new Sync($server, $debug);
    }

    // ------------------------------------------------------------------------------

    /**
     * get async request instance
     */
    public function getAsync()
    {
        $debug  = $this->debug;
        $server = $this->getServer();

        // when set log file
        if ($this->debug && !empty($this->logFile))
        {
            $debug = fopen($this->logFile, 'a');
        }

        return new Async($server, $debug);
    }

    // ------------------------------------------------------------------------------

}
