<?php

declare(strict_types = 1);

namespace Nylas\Management;

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
class Application
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
     * Return application IP addresses.
     *
     * @see https://developer.nylas.com/docs/api/v2/#get-/a/client_id/ip_addresses
     *
     * @return array
     * @throws GuzzleException
     */
    public function returnApplicationIPAddresses(): array
    {
        return $this->options
            ->getSync()
            ->setPath($this->options->getClientId())
            ->setHeaderParams($this->options->getAuthorizationHeader(false))
            ->get(API::LIST['ipAddresses']);
    }

    // ------------------------------------------------------------------------------

    /**
     * Return information about a Nylas application.
     *
     * @see https://developer.nylas.com/docs/api/v2/#get-/a/client_id
     *
     * @return array
     * @throws GuzzleException
     */
    public function returnApplicationDetails(): array
    {
        return $this->options
            ->getSync()
            ->setPath($this->options->getClientId())
            ->setHeaderParams($this->options->getAuthorizationHeader(false))
            ->get(API::LIST['manageApp']);
    }

    // ------------------------------------------------------------------------------

    /**
     * Update application details.
     *
     * @see https://developer.nylas.com/docs/api/v2/#post-/a/client_id
     *
     * @param array $params
     *
     * @return array
     * @throws GuzzleException
     */
    public function updateApplicationDetails(array $params): array
    {
        V::doValidate(V::keySet(
            V::key('redirect_uris', V::simpleArray(V::url())),
            V::keyOptional('application_name', V::stringType()::notEmpty()),
        ), $params);

        return $this->options
            ->getSync()
            ->setPath($this->options->getClientId())
            ->setFormParams($params)
            ->setHeaderParams($this->options->getAuthorizationHeader(false))
            ->put(API::LIST['manageApp']);
    }

    // ------------------------------------------------------------------------------
}
