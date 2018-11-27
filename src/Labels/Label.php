<?php namespace Nylas\Labels;

use Nylas\Utilities\API;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validate as V;
use Nylas\Exceptions\NylasException;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Labels
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/11/23
 */
class Label
{

    // ------------------------------------------------------------------------------

    /**
     * @var \Nylas\Utilities\Options
     */
    private $options;

    // ------------------------------------------------------------------------------

    /**
     * Label constructor.
     *
     * @param \Nylas\Utilities\Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    // ------------------------------------------------------------------------------

    /**
     * get labels list
     *
     * @param string $accessToken
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function getLabelsList(string $accessToken = null)
    {
        $accessToken = $accessToken ?? $this->options->getAccessToken();

        $rule = V::stringType()::notEmpty();

        if (!$rule->validate($accessToken))
        {
            throw new NylasException('invalid params');
        }

        $header = ['Authorization' => $accessToken];

        return $this->options
        ->getRequest()
        ->setHeaderParams($header)
        ->get(API::LIST['labels']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get label
     *
     * @param string $labelId
     * @param string $accessToken
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function getLabel(string $labelId, string $accessToken = null)
    {
        $params =
        [
            'id'           => $labelId,
            'access_token' => $accessToken ?? $this->options->getAccessToken(),
        ];

        $rule = V::keySet(
            V::key('id', V::stringType()::notEmpty()),
            V::key('access_token', V::stringType()::notEmpty())
        );

        if (!$rule->validate($params))
        {
            throw new NylasException('invalid params');
        }

        $path   = [$params['id']];
        $header = ['Authorization' => $params['access_token']];

        return $this->options
        ->getRequest()
        ->setPath($path)
        ->setHeaderParams($header)
        ->get(API::LIST['oneLabel']);
    }

    // ------------------------------------------------------------------------------

    /**
     * add label
     *
     * @param string $displayName
     * @param string $accessToken
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function addLabel(string $displayName, string $accessToken = null)
    {
        $params =
        [
            'display_name' => $displayName,
            'access_token' => $accessToken ?? $this->options->getAccessToken(),
        ];

        $rule = V::keySet(
            V::key('access_token', V::stringType()::notEmpty()),
            V::key('display_name', V::stringType()::notEmpty())
        );

        if (!$rule->validate($params))
        {
            throw new NylasException('invalid params');
        }

        $header = ['Authorization' => $params['access_token']];

        unset($params['access_token']);

        return $this->options
        ->getRequest()
        ->setFormParams($params)
        ->setHeaderParams($header)
        ->post(API::LIST['labels']);
    }

    // ------------------------------------------------------------------------------

    /**
     * update label
     *
     * @param array $params
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function updateLabel(array $params)
    {
        $params['access_token'] =
        $params['access_token'] ?? $this->options->getAccessToken();

        $rule = V::keySet(
            V::key('id', V::stringType()::notEmpty()),
            V::key('access_token', V::stringType()::notEmpty()),
            V::key('display_name', V::stringType()::notEmpty())
        );

        if (!$rule->validate($params))
        {
            throw new NylasException('invalid params');
        }

        $path   = [$params['id']];
        $header = ['Authorization' => $params['access_token']];

        unset($params['id'], $params['access_token']);

        return $this->options
        ->getRequest()
        ->setPath($path)
        ->setFormParams($params)
        ->setHeaderParams($header)
        ->put(API::LIST['oneLabel']);
    }

    // ------------------------------------------------------------------------------

    /**
     * delete label
     *
     * @param array $params
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function deleteLabel(array $params)
    {
        $params['access_token'] =
        $params['access_token'] ?? $this->options->getAccessToken();

        $rule = V::keySet(
            V::key('id', V::stringType()::notEmpty()),
            V::key('access_token', V::stringType()::notEmpty()),
            V::key('display_name', V::stringType()::notEmpty())
        );

        if (!$rule->validate($params))
        {
            throw new NylasException('invalid params');
        }

        $path   = [$params['id']];
        $header = ['Authorization' => $params['access_token']];

        unset($params['id'], $params['access_token']);

        return $this->options
        ->getRequest()
        ->setPath($path)
        ->setFormParams($params)
        ->setHeaderParams($header)
        ->delete(API::LIST['oneLabel']);
    }

    // ------------------------------------------------------------------------------

}
