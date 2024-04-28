<?php

declare(strict_types = 1);

namespace Nylas\Management;

use function count;
use function is_array;
use function array_keys;
use function array_values;

use Nylas\Utilities\API;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validator as V;
use GuzzleHttp\Exception\GuzzleException;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Account Manage
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2023/07/21
 */
class Account
{
    // ------------------------------------------------------------------------------

    /**
     * @var Options
     */
    private Options $options;

    // ------------------------------------------------------------------------------

    /**
     * Manage constructor.
     *
     * @param Options $options
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
     * @throws GuzzleException
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
     * @see https://developer.nylas.com/docs/api/v2/#get-/account
     *
     * @return array
     * @throws GuzzleException
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
     * @see https://developer.nylas.com/docs/api/v2/#get-/a/client_id/accounts
     *
     * @param array $params
     *
     * @return array
     * @throws GuzzleException
     */
    public function returnAllAccounts(array $params): array
    {
        V::doValidate(V::keySet(
            V::keyOptional('limit', V::intType()::min(1)),
            V::keyOptional('offset', V::intType()::min(0)),
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
     * @see https://developer.nylas.com/docs/api/v2/#get-/a/client_id/accounts/id
     *
     * @param string $accountId
     *
     * @return array
     * @throws GuzzleException
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
     * @see https://developer.nylas.com/docs/api/v2/#delete-/a/client_id/accounts/id
     *
     * @param string $accountId
     *
     * @return array
     * @throws GuzzleException
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
     * @see https://developer.nylas.com/docs/api/v2/#post-/a/client_id/accounts/id/downgrade
     *
     * @param string $accountId
     *
     * @return array
     * @throws GuzzleException
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
     * @see https://developer.nylas.com/docs/api/v2/#post-/a/client_id/accounts/id/upgrade
     *
     * @param string $accountId
     *
     * @return array
     * @throws GuzzleException
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
     * @see https://developer.nylas.com/docs/api/v2/#post-/a/client_id/accounts/id/revoke-all
     *
     * @param string  $accountId
     * @param ?string $keepAccessToken
     *
     * @return array
     * @throws GuzzleException
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
     * @see https://developer.nylas.com/docs/api/v2/#post-/a/client_id/accounts/id/token-info
     *
     * @param string  $accountId
     * @param ?string $accessToken
     *
     * @return array
     * @throws GuzzleException
     */
    public function returnTokenInformation(string $accountId, ?string $accessToken = null): array
    {
        $params = empty($accessToken) ? [] : ['access_token' => $accessToken];

        return $this->options
            ->getSync()
            ->setPath($this->options->getClientId(), $accountId)
            ->setFormParams($params)
            ->setHeaderParams($this->options->getAuthorizationHeader(false))
            ->post(API::LIST['tokenInfo']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get metadata array rules
     *
     * @see https://developer.nylas.com/docs/api/metadata/#keep-in-mind
     *
     * @return V
     */
    private static function metadataRules(): V
    {
        return V::callback(static function (mixed $input): bool
        {
            if (!is_array($input) || count($input) > 50)
            {
                return false;
            }

            $keys = array_keys($input);
            $isOk = V::each(V::stringType()::length(1, 40))->validate($keys);

            if (!$isOk)
            {
                return false;
            }

            // https://developer.nylas.com/docs/api/metadata/#delete-metadata
            return V::each(V::stringType()::length(0, 500))->validate(array_values($input));
        });
    }

    // ------------------------------------------------------------------------------
}
