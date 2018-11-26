<?php namespace Nylas\Utilities;

use Nylas\Exceptions\NylasException;

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

    // ------------------------------------------------------------------------------

    /**
     * @var string
     */
    private $server = (string) API::LIST['server'];

    // ------------------------------------------------------------------------------

    /**
     * @var string
     */
    private $clientId;

    // ------------------------------------------------------------------------------

    /**
     * @var string
     */
    private $clientSecret;

    // ------------------------------------------------------------------------------

    /**
     * @var string
     */
    private $accessToken;

    // ------------------------------------------------------------------------------

    /**
     * @var string
     */
    private $accountId;

    // ------------------------------------------------------------------------------

    /**
     * @var Request
     */
    private $request;

    // ------------------------------------------------------------------------------

    /**
     * Options constructor.
     *
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->debug       = $options['debug'] ?? false;
        $this->server      = $options['server'] ?? $this->server;
        $this->accountId   = $options['account_id'] ?? '';
        $this->accessToken = $options['access_token'] ?? '';

        if (empty($options['client_id']) || empty($options['client_secret']))
        {
            throw new NylasException('client_id & client_secret required');
        }

        $this->clientId     = $options['client_id'];
        $this->clientSecret = $options['client_secret'];
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
     * @param string|NULL $server
     * @param bool        $debug
     */
    public function resetRequest(string $server = null, bool $debug = null)
    {
        $debug  = $debug ?? $this->debug;
        $server = $server ?? $this->server;

        $this->request = new Request($server, $debug);
    }

    // ------------------------------------------------------------------------------

    /**
     * @return \Nylas\Utilities\Request
     */
    public function getRequest()
    {
        if (!$this->request instanceof Request)
        {
            $this->resetRequest();
        }

        return $this->request;
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
            'server'        => $this->server,
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'account_id'    => $this->accountId,
            'access_token'  => $this->accessToken,
        ];
    }

    // ------------------------------------------------------------------------------

}
