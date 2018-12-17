<?php namespace Nylas\Folders;

use Nylas\Utilities\API;
use Nylas\Utilities\Helper;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validate as V;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Folders
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/12/17
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
     * @return array
     */
    public function getFoldersList(string $accessToken = null)
    {
        $rule = V::stringType()->notEmpty();

        $accessToken = $accessToken ?? $this->options->getAccessToken();

        V::doValidate($rule, $accessToken);

        $header = ['Authorization' => $accessToken];

        return $this->options
        ->getSync()
        ->setHeaderParams($header)
        ->get(API::LIST['folders']);
    }

    // ------------------------------------------------------------------------------

    /**
     * add folder
     *
     * @param string $displayName
     * @param string $accessToken
     * @return array
     */
    public function addFolder(string $displayName = null, string $accessToken = null)
    {
        $params = ['access_token' => $accessToken ?? $this->options->getAccessToken()];

        !empty($displayName) AND $params['display_name'] = $displayName;

        $rule = V::keySet(
            V::key('access_token', V::stringType()->notEmpty()),
            V::keyOptional('display_name', V::stringType()->notEmpty())
        );

        V::doValidate($rule, $params);

        $header = ['Authorization' => $params['access_token']];

        unset($params['access_token']);

        return $this->options
        ->getSync()
        ->setFormParams($params)
        ->setHeaderParams($header)
        ->post(API::LIST['folders']);
    }

    // ------------------------------------------------------------------------------

    /**
     * update folder
     *
     * @param array $params
     * @return array
     */
    public function updateFolder(array $params)
    {
        $params['access_token'] =
        $params['access_token'] ?? $this->options->getAccessToken();

        $rule = V::keySet(
            V::key('id', V::stringType()->notEmpty()),
            V::key('access_token', V::stringType()->notEmpty()),
            V::keyOptional('display_name', V::stringType()->notEmpty())
        );

        V::doValidate($rule, $params);

        $path   = $params['id'];
        $header = ['Authorization' => $params['access_token']];

        unset($params['id'], $params['access_token']);

        return $this->options
        ->getSync()
        ->setPath($path)
        ->setFormParams($params)
        ->setHeaderParams($header)
        ->put(API::LIST['oneFolder']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get folder
     *
     * @param string|array $folderId
     * @param string $accessToken
     * @return array
     */
    public function getFolder($folderId, string $accessToken = null)
    {
        $folderId    = Helper::fooToArray($folderId);
        $accessToken = $accessToken ?? $this->options->getAccessToken();

        $rule = V::each(V::stringType()->notEmpty(), V::intType());

        V::doValidate($rule, $folderId);
        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $queues = [];
        $target = API::LIST['oneFolder'];
        $header = ['Authorization' => $accessToken];

        foreach ($folderId as $id)
        {
            $request = $this->options
            ->getAsync()
            ->setPath($id)
            ->setHeaderParams($header);

            $queues[] = function () use ($request, $target)
            {
                return $request->get($target);
            };
        }

        $pools = $this->options->getAsync()->pool($queues, false);

        return Helper::concatPoolInfos($folderId, $pools);
    }

    // ------------------------------------------------------------------------------

    /**
     * delete folder
     *
     * @param string|array $folderId
     * @param string $accessToken
     * @return array
     */
    public function deleteFolder($folderId, string $accessToken = null)
    {
        $folderId    = Helper::fooToArray($folderId);
        $accessToken = $accessToken ?? $this->options->getAccessToken();

        $rule = V::each(V::stringType()->notEmpty(), V::intType());

        V::doValidate($rule, $folderId);
        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $queues = [];
        $target = API::LIST['oneFolder'];
        $header = ['Authorization' => $accessToken];

        foreach ($folderId as $id)
        {
            $request = $this->options
            ->getAsync()
            ->setPath($id)
            ->setHeaderParams($header);

            $queues[] = function () use ($request, $target)
            {
                return $request->delete($target);
            };
        }

        $pools = $this->options->getAsync()->pool($queues, false);

        return Helper::concatPoolInfos($folderId, $pools);
    }

    // ------------------------------------------------------------------------------

}
