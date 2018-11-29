<?php namespace Nylas\Files;

use Nylas\Utilities\API;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validate as V;

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
     * @var \Nylas\Utilities\Options
     */
    private $options;

    // ------------------------------------------------------------------------------

    /**
     * File constructor.
     *
     * @param \Nylas\Utilities\Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    // ------------------------------------------------------------------------------

    /**
     * get files list
     *
     * @param array $params
     * @return array
     */
    public function getFilesList(array $params)
    {
        $params['access_token'] =
        $params['access_token'] ?? $this->options->getAccessToken();

        $rule = V::keySet(
            V::keyOptional('view', V::in(['count', 'ids'])),
            V::keyOptional('filename', V::stringType()->notEmpty()),
            V::keyOptional('message_id', V::stringType()->notEmpty()),
            V::keyOptional('content_type', V::stringType()->notEmpty()),

            V::key('access_token', V::stringType()->notEmpty())
        );

        V::doValidate($rule, $params);

        $header = ['Authorization' => $params['access_token']];

        unset($params['access_token']);

        return $this->options
        ->getRequest()
        ->setHeaderParams($header)
        ->get(API::LIST['files']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get file
     *
     * @param string $fileId
     * @param string $accessToken
     * @return array
     */
    public function getFile(string $fileId, string $accessToken = null)
    {
        $params =
        [
            'id'           => $fileId,
            'access_token' => $accessToken ?? $this->options->getAccessToken(),
        ];

        $rule = V::keySet(
            V::key('id', V::stringType()->notEmpty()),
            V::key('access_token', V::stringType()->notEmpty())
        );

        V::doValidate($rule, $params);

        $header = ['Authorization' => $params['access_token']];

        return $this->options
        ->getRequest()
        ->setPath($params['id'])
        ->setHeaderParams($header)
        ->get(API::LIST['oneFile']);
    }

    // ------------------------------------------------------------------------------

    /**
     * delete file
     *
     * @param string $fileId
     * @param string $accessToken
     * @return mixed
     */
    public function deleteFile(string $fileId, string $accessToken = null)
    {
        $params =
        [
            'id'           => $fileId,
            'access_token' => $accessToken ?? $this->options->getAccessToken(),
        ];

        $rule = V::keySet(
            V::key('id', V::stringType()->notEmpty()),
            V::key('access_token', V::stringType()->notEmpty())
        );

        V::doValidate($rule, $params);

        $header = ['Authorization' => $params['access_token']];

        return $this->options
        ->getRequest()
        ->setPath($params['id'])
        ->setHeaderParams($header)
        ->delete(API::LIST['oneFile']);
    }

    // ------------------------------------------------------------------------------

    /**
     * add file
     *
     * @param array $file
     * @param string $accessToken
     * @return array
     */
    public function uploadFile(array $file, string $accessToken = null)
    {
        $params =
        [
            'file'         => $file,
            'access_token' => $accessToken ?? $this->options->getAccessToken(),
        ];

        $rule = V::keySet(
            V::key('file', V::arrayType()->notEmpty()),
            V::key('access_token', V::stringType()->notEmpty())
        );

        V::doValidate($rule, $params);

        $header = ['Authorization' => $params['access_token']];

        unset($params['access_token']);

        return $this->options
        ->getRequest()
        ->setFormFiles($params)
        ->setHeaderParams($header)
        ->post(API::LIST['files']);
    }

    // ------------------------------------------------------------------------------

    /**
     * download file
     *
     * @param string $fileId
     * @param string $accessToken
     * @return mixed
     */
    public function downloadFile(string $fileId, string $accessToken = null)
    {
        $params =
        [
            'id'           => $fileId,
            'access_token' => $accessToken ?? $this->options->getAccessToken(),
        ];

        $rule = V::keySet(
            V::key('id', V::stringType()->notEmpty()),
            V::key('access_token', V::stringType()->notEmpty())
        );

        V::doValidate($rule, $params);

        $header = ['Authorization' => $params['access_token']];

        unset($params['access_token']);

        return $this->options
        ->getRequest()
        ->setHeaderParams($header)
        ->get(API::LIST['downloadFile']);
    }

    // ------------------------------------------------------------------------------

}
