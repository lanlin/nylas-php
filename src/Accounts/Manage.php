<?php

namespace Nylas\Accounts;

use Exception;
use Nylas\Utilities\API;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validator as V;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Account Manage
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2020/04/26
 */
class Manage
{
    // ------------------------------------------------------------------------------

    /**
     * @var \Nylas\Utilities\Options
     */
    private $options;

    // ------------------------------------------------------------------------------

    /**
     * Manage constructor.
     *
     * @param \Nylas\Utilities\Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    // ------------------------------------------------------------------------------

    /**
     * get accounts list
     *
     * @param array $params
     *
     * @return array
     */
    public function getAccountsList(array $params = []): array
    {
        $rules = V::keySet(
            V::keyOptional('limit', V::intType()->min(1)),
            V::keyOptional('offset', V::intType()->min(0))
        );

        V::doValidate($rules, $params);

        $client = $this->options->getClientApps();
        $header = ['Authorization' => $client['client_secret']];

        $pagination =
        [
            'limit'  => $params['limit'] ?? 100,
            'offset' => $params['offset'] ?? 0,
        ];

        return $this->options
            ->getSync()
            ->setPath($client['client_id'])
            ->setQuery($pagination)
            ->setHeaderParams($header)
            ->get(API::LIST['listAllAccounts']);
    }

    // ------------------------------------------------------------------------------

    /**
     * revoke all tokens
     *
     * @param array $params
     *
     * @return array
     */
    public function revokeAllTokens(array $params = []): array
    {
        $rules = V::keySet(
            V::keyOptional('keep_access_token', V::stringType()->notEmpty())
        );

        $accountId = $params['account_id'] ?? $this->options->getAccountId();

        unset($params['account_id']);
        V::doValidate($rules, $params);

        $client = $this->options->getClientApps();
        $header = ['Authorization' => $client['client_secret']];

        return $this->options
            ->getSync()
            ->setPath($client['client_id'], $accountId)
            ->setFormParams($params)
            ->setHeaderParams($header)
            ->post(API::LIST['revokeAllTokens']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get account info
     *
     * @return array
     */
    public function getAccountInfo(): array
    {
        $accountId = $this->options->getAccountId();

        $client = $this->options->getClientApps();
        $header = ['Authorization' => $client['client_secret']];

        return $this->options
            ->getSync()
            ->setPath($client['client_id'], $accountId)
            ->setHeaderParams($header)
            ->get(API::LIST['listAnAccount']);
    }

    // ------------------------------------------------------------------------------

    /**
     * re-active account
     *
     * @return array
     */
    public function reactiveAccount(): array
    {
        $accountId = $this->options->getAccountId();

        $client = $this->options->getClientApps();
        $header = ['Authorization' => $client['client_secret']];

        return $this->options
            ->getSync()
            ->setPath($client['client_id'], $accountId)
            ->setHeaderParams($header)
            ->post(API::LIST['reactiveAnAccount']);
    }

    // ------------------------------------------------------------------------------

    /**
     * cancel account
     *
     * @return array
     */
    public function cancelAccount(): array
    {
        $accountId = $this->options->getAccountId();

        $client = $this->options->getClientApps();
        $header = ['Authorization' => $client['client_secret']];

        return $this->options
            ->getSync()
            ->setPath($client['client_id'], $accountId)
            ->setHeaderParams($header)
            ->post(API::LIST['cancelAnAccount']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get ip addresses
     *
     * @return array
     */
    public function getIpAddresses(): array
    {
        $client = $this->options->getClientApps();
        $header = ['Authorization' => $client['client_secret']];

        return $this->options
            ->getSync()
            ->setPath($client['client_id'])
            ->setHeaderParams($header)
            ->get(API::LIST['ipAddresses']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get information about an account's access_token
     *
     * @throws Exception
     *
     * @return array
     */
    public function getTokenInfo(): array
    {
        $accountId = $this->options->getAccountId();

        $client = $this->options->getClientApps();
        $header = ['Authorization' => $client['client_secret']];

        return $this->options
            ->getSync()
            ->setPath($client['client_id'], $accountId)
            ->setHeaderParams($header)
            ->get(API::LIST['tokenInfo']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get information about a Nylas application
     *
     * @throws Exception
     *
     * @return array
     */
    public function getApplication(): array
    {
        $client = $this->options->getClientApps();
        $header = ['Authorization' => $client['client_secret']];

        return $this->options
            ->getSync()
            ->setPath($client['client_id'])
            ->setHeaderParams($header)
            ->get(API::LIST['manageApp']);
    }

    // ------------------------------------------------------------------------------

    /**
     * update details of a Nylas application
     *
     * @param array $params
     *
     * @throws Exception
     *
     * @return array
     */
    public function updateApplication(array $params): array
    {
        $rules = V::keySet(
            V::keyOptional('application_name', V::stringType()->notEmpty()),
            V::key('redirect_uris', V::simpleArray(V::url())),
        );

        V::doValidate($rules, $params);

        $client = $this->options->getClientApps();
        $header = ['Authorization' => $client['client_secret']];

        return $this->options
            ->getSync()
            ->setPath($client['client_id'])
            ->setFormParams($params)
            ->setHeaderParams($header)
            ->put(API::LIST['manageApp']);
    }

    // ------------------------------------------------------------------------------
}
