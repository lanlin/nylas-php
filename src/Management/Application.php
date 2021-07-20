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
 * @change 2021/07/20
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
