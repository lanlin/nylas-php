<?php namespace Nylas\Folders;

use Nylas\Utilities\API;
use Nylas\Utilities\Request;
use Nylas\Utilities\Validate as V;
use Nylas\Exceptions\NylasException;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Folders
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/11/23
 */
class Folder
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
     * get folders list
     *
     * @param string $accessToken
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function getFoldersList(string $accessToken)
    {
        $rule = V::stringType()::notEmpty();

        if (!$rule->validate($accessToken))
        {
            throw new NylasException('invalid params');
        }

        $header = ['Authorization' => $accessToken];

        return $this->request->setHeaderParams($header)->get(API::LIST['folders']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get folder
     *
     * @param array $params
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function getFolder(array $params)
    {
        $rule = V::keySet(
            V::key('folder_id', V::stringType()::notEmpty()),
            V::key('access_token', V::stringType()::notEmpty())
        );

        if (!$rule->validate($params))
        {
            throw new NylasException('invalid params');
        }

        $path   = [$params['folder_id']];
        $header = ['Authorization' => $params['access_token']];

        return $this->request
        ->setPath($path)
        ->setHeaderParams($header)
        ->get(API::LIST['oneFolder']);
    }

    // ------------------------------------------------------------------------------

    /**
     * add folder
     *
     * @param array $params
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function addFolder(array $params)
    {
        $rule = V::keySet(
            V::key('access_token', V::stringType()::notEmpty()),
            V::key('display_name', V::stringType()::notEmpty(), false)
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
        ->post(API::LIST['folders']);
    }

    // ------------------------------------------------------------------------------

    /**
     * update folder
     *
     * @param array $params
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function updateFolder(array $params)
    {
        $rule = V::keySet(
            V::key('folder_id', V::stringType()::notEmpty()),
            V::key('access_token', V::stringType()::notEmpty()),
            V::key('display_name', V::stringType()::notEmpty(), false)
        );

        if (!$rule->validate($params))
        {
            throw new NylasException('invalid params');
        }

        $path   = [$params['folder_id']];
        $header = ['Authorization' => $params['access_token']];

        unset($params['access_token']);

        return $this->request
        ->setPath($path)
        ->setFormParams($params)
        ->setHeaderParams($header)
        ->put(API::LIST['oneFolder']);
    }

    // ------------------------------------------------------------------------------

    /**
     * delete folder
     *
     * @param array $params
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function deleteFolder(array $params)
    {
        $rule = V::keySet(
            V::key('folder_id', V::stringType()::notEmpty()),
            V::key('access_token', V::stringType()::notEmpty())
        );

        if (!$rule->validate($params))
        {
            throw new NylasException('invalid params');
        }

        $path   = [$params['folder_id']];
        $header = ['Authorization' => $params['access_token']];

        return $this->request
        ->setPath($path)
        ->setHeaderParams($header)
        ->delete(API::LIST['oneFolder']);
    }

    // ------------------------------------------------------------------------------

}
