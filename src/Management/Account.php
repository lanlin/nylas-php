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
 * @change 2022/01/27
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
     * Add Account Metadata
     *
     * @see https://developer.nylas.com/docs/api#put/a/client_id/accounts/id
     *
     * @param string $accountId
     * @param array  $metadata
     *
     * @return array
     */
    public function addAccountMetadata(string $accountId, array $metadata): array
    {
        V::doValidate(self::metadataRules(), $metadata);

        return $this->options
            ->getSync()
            ->setPath($this->options->getClientId(), $accountId)
            ->setHeaderParams($this->options->getAuthorizationHeader(false))
            ->put(API::LIST['listAnAccount']);
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
     * @param array $params
     *
     * @return array
     */
    public function returnAllAccounts(array $params): array
    {
        V::doValidate(V::keySet(
            V::keyOptional('limit', V::intType()->min(1)),
            V::keyOptional('offset', V::intType()->min(0)),

            // @see https://developer.nylas.com/docs/developer-tools/api/metadata/#keep-in-mind
            V::keyOptional('metadata_key', V::stringType()->length(1, 40)),
            V::keyOptional('metadata_value', V::stringType()->length(1, 500)),
            V::keyOptional('metadata_paire', V::stringType()->length(3, 27100)),
            V::keyOptional('metadata_search', V::stringType()->notEmpty())
        ), $params);

        return $this->options
            ->getSync()
            ->setPath($this->options->getClientId())
            ->setQuery($params)
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
     * @param string  $accountId
     * @param array   $params
     * @param ?string $keepAccessToken
     *
     * @return array
     */
    public function revokeAllTokens(string $accountId, ?string $keepAccessToken = null): array
    {
        $keep = empty($keepAccessToken) ? [] : ['keep_access_token' => $keepAccessToken];

        return $this->options
            ->getSync()
            ->setPath($this->options->getClientId(), $accountId)
            ->setFormParams($keep)
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

    /**
     * get metadata array rules
     *
     * @see https://developer.nylas.com/docs/developer-tools/api/metadata/#keep-in-mind
     *
     * @return \Nylas\Utilities\Validator
     */
    private static function metadataRules(): V
    {
        return V::callback(static function (mixed $input): bool
        {
            if (!\is_array($input) || \count($input) > 50)
            {
                return false;
            }

            $keys = \array_keys($input);
            $isOk = V::each(V::stringType()->length(1, 40))->validate($keys);

            if (!$isOk)
            {
                return false;
            }

            // https://developer.nylas.com/docs/developer-tools/api/metadata/#delete-metadata
            return V::each(V::stringType()->length(0, 500))->validate(\array_values($input));
        });
    }

    // ------------------------------------------------------------------------------
}
