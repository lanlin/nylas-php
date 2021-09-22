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
