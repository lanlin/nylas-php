<?php namespace Nylas\Accounts;

use Nylas\Utilities\API;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validate as V;
use Nylas\Authentication\Hosted;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Account
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/11/22
 */
class Account
{

    // ------------------------------------------------------------------------------

    /**
     * @var \Nylas\Utilities\Options
     */
    private $options;

    // ------------------------------------------------------------------------------

    /**
     * Account constructor.
     *
     * @param \Nylas\Utilities\Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    // ------------------------------------------------------------------------------

    /**
     * get account info with access_token
     *
     * @param string $accessToken
     * @return mixed
     */
    public function getAccount(string $accessToken = null)
    {
        $accessToken = $accessToken ?? $this->options->getAccessToken();

        V::stringType()::notEmpty()->assert($accessToken);

        $header = ['Authorization' => $accessToken];

        return $this->options
        ->getRequest()
        ->setHeaderParams($header)
        ->get(API::LIST['account']);
    }

    // ------------------------------------------------------------------------------

    /**
     * cancel account with access_token
     *
     * @param string $accessToken
     * @return mixed
     */
    public function cancelAccount(string $accessToken = null)
    {
        $accessToken = $accessToken ?? $this->options->getAccessToken();

        V::stringType()::notEmpty()->assert($accessToken);

        return (new Hosted($this->options))->postOAuthRevoke($accessToken);
    }

    // ------------------------------------------------------------------------------

}
