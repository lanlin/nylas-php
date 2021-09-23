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
        return $this->options
            ->getSync()
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->get(API::LIST['account']);
    }

    // ------------------------------------------------------------------------------

    /**
     * Return All Accounts
     *
     * @see https://developer.nylas.com/docs/api/#get/a/client_id/accounts
     *
     * @param int   $offset
     * @param int   $limit
     *
     * @return array
     */
    public function returnAllAccounts(int $offset = 0, int $limit = 100): array
    {
        return $this->options
            ->getSync()
            ->setPath($this->options->getClientId())
            ->setQuery(['limit' => $limit, 'offset' => $offset])
            ->setHeaderParams($this->options->getAuthorizationHeader(false))
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
        return $this->options
            ->getSync()
            ->setPath($this->options->getClientId(), $accountId)
            ->setHeaderParams($this->options->getAuthorizationHeader(false))
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
        return $this->options
            ->getSync()
            ->setPath($this->options->getClientId(), $accountId)
            ->setHeaderParams($this->options->getAuthorizationHeader(false))
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
        return $this->options
            ->getSync()
            ->setPath($this->options->getClientId(), $accountId)
            ->setHeaderParams($this->options->getAuthorizationHeader(false))
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
        return $this->options
            ->getSync()
            ->setPath($this->options->getClientId(), $accountId)
            ->setHeaderParams($this->options->getAuthorizationHeader(false))
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
    public function revokeAllTokens(string $accountId, ?string $keepAccessToken = null): array
    {
        return $this->options
            ->getSync()
            ->setPath($this->options->getClientId(), $accountId)
            ->setFormParams(['keep_access_token' => $keepAccessToken])
            ->setHeaderParams($this->options->getAuthorizationHeader(false))
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
        return $this->options
            ->getSync()
            ->setPath($this->options->getClientId(), $accountId)
            ->setHeaderParams($this->options->getAuthorizationHeader(false))
            ->get(API::LIST['tokenInfo']);
    }

    // ------------------------------------------------------------------------------
}
