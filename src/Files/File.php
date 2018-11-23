<?php namespace Nylas\Files;

use Nylas\Utilities\API;
use Nylas\Utilities\Request;
use Nylas\Utilities\Validate as V;
use Nylas\Exceptions\NylasException;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Files
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/11/23
 */
class File
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
     * get files list
     *
     * @param array $params
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function getFilesList(array $params)
    {
        $rule = V::keySet(
            V::key('view', V::in(['count', 'ids']), false),
            V::key('filename', V::stringType()::notEmpty(), false),
            V::key('message_id', V::stringType()::notEmpty(), false),
            V::key('content_type', V::stringType()::notEmpty(), false),
            V::key('access_token', V::stringType()::notEmpty())
        );

        if (!$rule->validate($params))
        {
            throw new NylasException('invalid params');
        }

        $header = ['Authorization' => $params['access_token']];

        unset($params['access_token']);

        return $this->request->setHeaderParams($header)->get(API::LIST['files']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get file
     *
     * @param array $params
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function getFile(array $params)
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
        ->get(API::LIST['oneFile']);
    }

    // ------------------------------------------------------------------------------

    /**
     * delete file
     *
     * @param array $params
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function deleteFile(array $params)
    {
        $rule = V::keySet(
            V::key('file_id', V::stringType()::notEmpty()),
            V::key('access_token', V::stringType()::notEmpty())
        );

        if (!$rule->validate($params))
        {
            throw new NylasException('invalid params');
        }

        $path   = [$params['file_id']];
        $header = ['Authorization' => $params['access_token']];

        return $this->request
        ->setPath($path)
        ->setHeaderParams($header)
        ->delete(API::LIST['oneFile']);
    }

    // ------------------------------------------------------------------------------

    /**
     * add file
     *
     * @param array $params
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function uploadFile(array $params)
    {
        $rule = V::keySet(
            V::key('file', V::arrayType()::notEmpty()),
            V::key('access_token', V::stringType()::notEmpty())
        );

        if (!$rule->validate($params))
        {
            throw new NylasException('invalid params');
        }

        $header = ['Authorization' => $params['access_token']];

        unset($params['access_token']);

        return $this->request
        ->setFormFiles($params)
        ->setHeaderParams($header)
        ->post(API::LIST['files']);
    }

    // ------------------------------------------------------------------------------

    /**
     * download file
     *
     * @param array $params
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function downloadFile(array $params)
    {
        $rule = V::keySet(
            V::key('id', V::stringType()::notEmpty()),
            V::key('access_token', V::stringType()::notEmpty(), false)
        );

        if (!$rule->validate($params))
        {
            throw new NylasException('invalid params');
        }

        $header = ['Authorization' => $params['access_token']];

        unset($params['access_token']);

        return $this->request->setHeaderParams($header)->get(API::LIST['downloadFile']);
    }

    // ------------------------------------------------------------------------------

}
