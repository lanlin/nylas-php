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
 * @change 2021/09/22
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
     * Return Account Details
     *
     * @see https://developer.nylas.com/docs/api/#get/account
     *
     * @return array
     */
    public function returnAccountDetails(): array
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
     * Return All Accounts
     *
     * @see https://developer.nylas.com/docs/api/#get/a/client_id/accounts
     *
     * @param array $params
     *
     * @return array
     */
    public function returnAllAccounts(array $params = []): array
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
     * Returns details from a single account.
     *
     * @see https://developer.nylas.com/docs/api/#get/a/client_id/accounts/id
     *
     * @param string $accountId
     *
     * @return array
     */
    public function returnAnAccount(string $accountId): array
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
     * Deletes an account. Accounts deleted using this method are immediately unavailable.
     *
     * @see https://developer.nylas.com/docs/api/#delete/a/client_id/accounts/id
     *
     * @param string $accountId
     *
     * @return array
     */
    public function deleteAnAccount(string $accountId): array
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
     * Cancels a paid Nylas account. Accounts that are cancelled instead of deleted, can be recovered within 3 days.
     *
     * @see https://developer.nylas.com/docs/api/#post/a/client_id/accounts/id/downgrade
     *
     * @param string $accountId
     *
     * @return array
     */
    public function cancelAnAccount(string $accountId): array
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
     * Reactivate a cancelled account.
     *
     * @see https://developer.nylas.com/docs/api/#post/a/client_id/accounts/id/upgrade
     *
     * @param string $accountId
     *
     * @return array
     */
    public function reactiveAnAccount(string $accountId): array
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

    /**
     * Revoke all access tokens for an account.
     *
     * @see https://developer.nylas.com/docs/api/#post/a/client_id/accounts/id/revoke-all
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
     * Return information about an accounts access token.
     *
     * @see https://developer.nylas.com/docs/api/#post/a/client_id/accounts/id/token-info
     *
     * @param string $accountId
     *
     * @return array
     */
    public function returnTokenInformation(string $accountId): array
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
}
