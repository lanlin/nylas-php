<?php namespace Nylas\Accounts;

use Nylas\Utilities\API;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validate as V;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Account Manage
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/11/22
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
     * @return mixed
     */
    public function getAccountsList(array $params = [])
    {
        $rules = V::keySet(
            V::keyOptional('limit', V::intType()::min(1)),
            V::keyOptional('offset', V::intType()::min(0))
        );

        $rules->assert($params);

        $client = $this->options->getClientApps();
        $header = ['Authorization' => $client['client_secret']];

        $pagination =
        [
            'limit'  => $params['limit'] ?? 100,
            'offset' => $params['offset'] ?? 0,
        ];

        return $this->options
        ->getRequest()
        ->setPath($client['client_id'])
        ->setQuery($pagination)
        ->setHeaderParams($header)
        ->get(API::LIST['listAllAccounts']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get account info
     *
     * @param string $accountId
     * @return mixed
     */
    public function getAccountInfo(string $accountId = null)
    {
        $accountId = $accountId ?? $this->options->getAccountId();

        $client = $this->options->getClientApps();
        $header = ['Authorization' => $client['client_secret']];

        return $this->options
        ->getRequest()
        ->setPath($client['client_id'], $accountId)
        ->setHeaderParams($header)
        ->get(API::LIST['listAnAccount']);
    }

    // ------------------------------------------------------------------------------

    /**
     * re-active account
     *
     * @param string $accountId
     * @return mixed
     */
    public function reactiveAccount(string $accountId = null)
    {
        $accountId = $accountId ?? $this->options->getAccountId();

        $client = $this->options->getClientApps();
        $header = ['Authorization' => $client['client_secret']];

        return $this->options
        ->getRequest()
        ->setPath($client['client_id'], $accountId)
        ->setHeaderParams($header)
        ->post(API::LIST['reactiveAnAccount']);
    }

    // ------------------------------------------------------------------------------

    /**
     * cancel account
     *
     * @param string $accountId
     * @return mixed
     */
    public function cancelAccount(string $accountId = null)
    {
        $accountId = $accountId ?? $this->options->getAccountId();

        $client    = $this->options->getClientApps();
        $header = ['Authorization' => $client['client_secret']];

        return $this->options
        ->getRequest()
        ->setPath($client['client_id'], $accountId)
        ->setHeaderParams($header)
        ->post(API::LIST['cancelAnAccount']);
    }

    // ------------------------------------------------------------------------------

}
