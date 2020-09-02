<?php

namespace Nylas\Accounts;

use Nylas\Utilities\API;
use Nylas\Utilities\Options;
use Nylas\Authentication\Hosted;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Account
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2020/04/26
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
     * cancel account
     *
     * @return array
     */
    public function cancelAccount(): array
    {
        return (new Hosted($this->options))->postOAuthRevoke();
    }

    // ------------------------------------------------------------------------------

    /**
     * get account info
     *
     * @return array
     */
    public function getAccount(): array
    {
        $accessToken = $this->options->getAccessToken();

        $header = ['Authorization' => $accessToken];

        return $this->options
            ->getSync()
            ->setHeaderParams($header)
            ->get(API::LIST['account']);
    }

    // ------------------------------------------------------------------------------
}
