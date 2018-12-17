<?php namespace Nylas\Deltas;

use Nylas\Utilities\API;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validate as V;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Deltas
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/12/17
 */
class Delta
{

    // ------------------------------------------------------------------------------

    /**
     * @var \Nylas\Utilities\Options
     */
    private $options;

    // ------------------------------------------------------------------------------

    /**
     * Delta constructor.
     *
     * @param \Nylas\Utilities\Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    // ------------------------------------------------------------------------------

    /**
     * get latest cursor
     *
     * @param string $accessToken
     * @return array
     */
    public function getLatestCursor(string $accessToken = null)
    {
        $accessToken = $accessToken ?? $this->options->getAccessToken();

        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $header = ['Authorization' => $accessToken];

        return $this->options
        ->getSync()
        ->setHeaderParams($header)
        ->post(API::LIST['deltaLatestCursor']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get a set of deltas
     *
     * @param array $params
     * @param string $accessToken
     * @return array
     */
    public function getSetOfDeltas(array $params, string $accessToken = null)
    {
        $rules = $this->getBaseRules();

        $accessToken = $accessToken ?? $this->options->getAccessToken();

        V::doValidate(V::keySet(...$rules), $params);
        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $header = ['Authorization' => $accessToken];

        return $this->options
        ->getSync()
        ->setQuery($params)
        ->setHeaderParams($header)
        ->get(API::LIST['delta']);
    }

    // ------------------------------------------------------------------------------

    /**
     * long polling delta updates
     *
     * @param array $params
     * @param string $accessToken
     * @return array
     */
    public function longPollingDelta(array $params, string $accessToken = null)
    {
        $rules = $this->getBaseRules();
        $times = V::key('timeout', V::intType()->min(1));

        array_push($rules, $times);

        $accessToken = $accessToken ?? $this->options->getAccessToken();

        V::doValidate(V::keySet(...$rules), $params);
        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $header = ['Authorization' => $accessToken];

        return $this->options
        ->getSync()
        ->setQuery($params)
        ->setHeaderParams($header)
        ->get(API::LIST['deltaLongpoll']);
    }

    // ------------------------------------------------------------------------------

    /**
     * streaming delta updates
     *
     * @param array $params
     * @param string $accessToken
     * @return mixed
     */
    public function streamingDelta(array $params, string $accessToken = null)
    {
        $rules = $this->getBaseRules();

        $accessToken = $accessToken ?? $this->options->getAccessToken();

        V::doValidate(V::keySet(...$rules), $params);
        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $header = ['Authorization' => $accessToken];

        return $this->options
        ->getSync()
        ->setQuery($params)
        ->setHeaderParams($header)
        ->get(API::LIST['deltaStreaming']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get base rules
     *
     * @return array
     */
    private function getBaseRules()
    {
        $types = ['contact', 'event', 'file', 'message', 'draft', 'thread', 'folder', 'label'];

        return
        [
            V::key('cursor', V::stringType()->notEmpty()),

            V::keyOptional('view', V::stringType()->notEmpty()),
            V::keyOptional('exclude_types', V::in($types)),
            V::keyOptional('include_types', V::in($types))
        ];
    }

    // ------------------------------------------------------------------------------

}
