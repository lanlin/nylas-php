<?php namespace Nylas\Folders;

use Nylas\Utilities\API;
use Nylas\Utilities\Options;
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
     * @var \Nylas\Utilities\Options
     */
    private $options;

    // ------------------------------------------------------------------------------

    /**
     * Folder constructor.
     *
     * @param \Nylas\Utilities\Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    // ------------------------------------------------------------------------------

    /**
     * get folders list
     *
     * @param string $accessToken
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function getFoldersList(string $accessToken = null)
    {
        $rule = V::stringType()::notEmpty();

        $accessToken = $accessToken ?? $this->options->getAccessToken();

        if (!$rule->validate($accessToken))
        {
            throw new NylasException('invalid params');
        }

        $header = ['Authorization' => $accessToken];

        return $this->options
        ->getRequest()
        ->setHeaderParams($header)
        ->get(API::LIST['folders']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get folder
     *
     * @param string $folderId
     * @param string $accessToken
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function getFolder(string $folderId, string $accessToken = null)
    {
        $params =
        [
            'id'           => $folderId,
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
        ->get(API::LIST['oneFolder']);
    }

    // ------------------------------------------------------------------------------

    /**
     * add folder
     *
     * @param string $displayName
     * @param string $accessToken
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function addFolder(string $displayName = null, string $accessToken = null)
    {
        $params = ['access_token' => $accessToken ?? $this->options->getAccessToken()];

        !empty($displayName) AND $params['dispaly_name'] = $displayName;

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

        return $this->options
        ->getRequest()
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
        $params['access_token'] =
        $params['access_token'] ?? $this->options->getAccessToken();

        $rule = V::keySet(
            V::key('id', V::stringType()::notEmpty()),
            V::key('access_token', V::stringType()::notEmpty()),
            V::key('display_name', V::stringType()::notEmpty(), false)
        );

        if (!$rule->validate($params))
        {
            throw new NylasException('invalid params');
        }

        $path   = [$params['id']];
        $header = ['Authorization' => $params['access_token']];

        unset($params['access_token']);

        return $this->options
        ->getRequest()
        ->setPath($path)
        ->setFormParams($params)
        ->setHeaderParams($header)
        ->put(API::LIST['oneFolder']);
    }

    // ------------------------------------------------------------------------------

    /**
     * delete folder
     *
     * @param string $folderId
     * @param string $accessToken
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function deleteFolder(string $folderId, string $accessToken = null)
    {
        $params =
        [
            'id'           => $folderId,
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
        ->delete(API::LIST['oneFolder']);
    }

    // ------------------------------------------------------------------------------

}
