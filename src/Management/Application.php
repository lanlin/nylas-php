<?php

namespace Nylas\Management;

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
 * @change 2021/09/22
 */
class Application
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
     * Return application IP addresses.
     *
     * @see https://developer.nylas.com/docs/api/#get/a/client_id/ip_addresses
     *
     * @return array
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
     * @see https://developer.nylas.com/docs/api/#get/a/client_id
     *
     * @throws Exception
     *
     * @return array
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
     * @see https://developer.nylas.com/docs/api/#post/a/client_id
     *
     * @param array $params
     *
     * @throws Exception
     *
     * @return array
     */
    public function updateApplicationDetails(array $params): array
    {
        V::doValidate(V::keySet(
            V::key('redirect_uris', V::simpleArray(V::url())),
            V::keyOptional('application_name', V::stringType()->notEmpty()),
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
