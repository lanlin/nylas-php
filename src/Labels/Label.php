<?php namespace Nylas\Labels;

use Nylas\Utilities\API;
use Nylas\Utilities\Request;
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
     * @var Request
     */
    private $request;

    // ------------------------------------------------------------------------------

    /**
     * Hosted constructor.
     */
    public function __construct()
    {
        $this->request = new Request();
    }

    // ------------------------------------------------------------------------------

    /**
     * get labels list
     *
     * @param string $accessToken
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function getLabelsList(string $accessToken)
    {
        $rule = V::stringType()::notEmpty();

        if (!$rule->validate($accessToken))
        {
            throw new NylasException('invalid params');
        }

        $header = ['Authorization' => $accessToken];

        return $this->request->setHeaderParams($header)->get(API::LIST['labels']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get label
     *
     * @param array $params
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function getLabel(array $params)
    {
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

        return $this->request
        ->setPath($path)
        ->setHeaderParams($header)
        ->get(API::LIST['oneLabel']);
    }

    // ------------------------------------------------------------------------------

    /**
     * add label
     *
     * @param array $params
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function addLabel(array $params)
    {
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

        return $this->request
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

        return $this->request
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

        return $this->request
        ->setPath($path)
        ->setFormParams($params)
        ->setHeaderParams($header)
        ->delete(API::LIST['oneLabel']);
    }

    // ------------------------------------------------------------------------------

}
