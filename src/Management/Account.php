<?php

namespace Nylas\Management;

use Nylas\Utilities\API;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validator as V;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Account Manage
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2021/07/20
 */
class Account
{
    // ------------------------------------------------------------------------------

    /**
     * @var \Nylas\Utilities\Options
     */
    private Options $options;

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
     * get account detail
     *
     * @return array
     */
    public function getAccountDetail(): array
    {
        $accessToken = $this->options->getAccessToken();

        $header = ['Authorization' => $accessToken];

        return $this->options
            ->getSync()
            ->setHeaderParams($header)
            ->get(API::LIST['account']);
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
     * get an account info
     *
     * @param string $accountId
     *
     * @return array
     */
    public function getAccountInfo(string $accountId): array
    {
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
     * delete an account
     *
     * @param string $accountId
     *
     * @return array
     */
    public function deleteAccount(string $accountId): array
    {
        $client = $this->options->getClientApps();
        $header = ['Authorization' => $client['client_secret']];

        return $this->options
            ->getSync()
            ->setPath($client['client_id'], $accountId)
            ->setHeaderParams($header)
            ->delete(API::LIST['listAnAccount']);
    }

    // ------------------------------------------------------------------------------

    /**
     * cancel account
     *
     * @param string $accountId
     *
     * @return array
     */
    public function cancelAccount(string $accountId): array
    {
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
     * revoke all tokens
     *
     * @param string $accountId
     * @param array  $params
     *
     * @return array
     */
    public function revokeAllTokens(string $accountId, array $params = []): array
    {
        $rules = V::keySet(
            V::keyOptional('keep_access_token', V::stringType()->notEmpty())
        );

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
     * get information about an account's access_token
     *
     * @param string $accountId
     *
     * @return array
     */
    public function getTokenInfo(string $accountId): array
    {
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
     * re-active account
     *
     * @param string $accountId
     *
     * @return array
     */
    public function reactiveAccount(string $accountId): array
    {
        $client = $this->options->getClientApps();
        $header = ['Authorization' => $client['client_secret']];

        return $this->options
            ->getSync()
            ->setPath($client['client_id'], $accountId)
            ->setHeaderParams($header)
            ->post(API::LIST['reactiveAnAccount']);
    }

    // ------------------------------------------------------------------------------
}
