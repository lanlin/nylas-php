<?php namespace Nylas\Accounts;

use Nylas\Utilities\API;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validate as V;
use Nylas\Exceptions\NylasException;

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
     * @throws \Nylas\Exceptions\NylasException
     */
    public function getAccountsList(array $params = [])
    {
        $rules = V::keySet(
            V::key('limit', V::intType()::min(1), false),
            V::key('offset', V::intType()::min(0), false)
        );

        if (!$rules->validate($params))
        {
            throw new NylasException('invalid params');
        }

        $client = $this->options->getClientApps();
        $path   = [$client['client_id']];
        $header = ['Authorization' => $client['client_secret']];

        $pagination =
        [
            'limit'  => $params['limit'] ?? 100,
            'offset' => $params['offset'] ?? 0,
        ];

        return $this->options
        ->getRequest()
        ->setPath($path)
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
        $client    = $this->options->getClientApps();
        $accountId = $accountId ?? $this->options->getAccountId();

        $path   = [$client['client_id'], $accountId];
        $header = ['Authorization' => $client['client_secret']];

        return $this->options
        ->getRequest()
        ->setPath($path)
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
        $client    = $this->options->getClientApps();
        $accountId = $accountId ?? $this->options->getAccountId();

        $path   = [$client['client_id'], $accountId];
        $header = ['Authorization' => $client['client_secret']];

        return $this->options
        ->getRequest()
        ->setPath($path)
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
        $client    = $this->options->getClientApps();
        $accountId = $accountId ?? $this->options->getAccountId();

        $path   = [$client['client_id'], $accountId];
        $header = ['Authorization' => $client['client_secret']];

        return $this->options
        ->getRequest()
        ->setPath($path)
        ->setHeaderParams($header)
        ->post(API::LIST['cancelAnAccount']);
    }

    // ------------------------------------------------------------------------------

}
