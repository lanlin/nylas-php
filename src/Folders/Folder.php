<?php

namespace Nylas\Folders;

use Nylas\Utilities\API;
use Nylas\Utilities\Helper;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validator as V;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Folders
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2020/09/30
 */
class Folder
{
    // ------------------------------------------------------------------------------

    /**
     * @var \Nylas\Utilities\Options
     */
    private Options $options;

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
     * @param string $view ids|count
     *
     * @return array
     */
    public function getFoldersList(?string $view = null): array
    {
        Helper::checkProviderUnit($this->options, false);

        $params = [
            'view'         => $view,
            'access_token' => $this->options->getAccessToken(),
        ];

        $rule = V::keySet(
            V::key('access_token', V::stringType()->notEmpty()),
            V::keyOptional('view', V::in(['ids', 'count'])),
        );

        V::doValidate($rule, $params);

        $header = ['Authorization' => $params['access_token']];
        $query  = empty($params['view']) ? [] : ['view' => $params['view']];

        return $this->options
            ->getSync()
            ->setQuery($query)
            ->setHeaderParams($header)
            ->get(API::LIST['folders']);
    }

    // ------------------------------------------------------------------------------

    /**
     * add folder
     *
     * @param string $displayName
     *
     * @return array
     */
    public function addFolder(?string $displayName = null): array
    {
        Helper::checkProviderUnit($this->options, false);

        $params = !empty($displayName) ? ['display_name' => $displayName] : [];

        $accessToken = $this->options->getAccessToken();

        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $header = ['Authorization' => $accessToken];

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
     *
     * @return array
     */
    public function updateFolder(array $params): array
    {
        Helper::checkProviderUnit($this->options, false);

        $accessToken = $this->options->getAccessToken();

        $rule = V::keySet(
            V::key('id', V::stringType()->notEmpty()),
            V::keyOptional('display_name', V::stringType()->notEmpty())
        );

        V::doValidate($rule, $params);
        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $path   = $params['id'];
        $header = ['Authorization' => $accessToken];

        unset($params['id']);

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
     * @param mixed $folderId string|string[]
     *
     * @return array
     */
    public function getFolder($folderId): array
    {
        Helper::checkProviderUnit($this->options, false);

        $folderId    = Helper::fooToArray($folderId);
        $accessToken = $this->options->getAccessToken();

        $rule = V::simpleArray(V::stringType()->notEmpty());

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

            $queues[] = static function () use ($request, $target)
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
     * @param mixed $folderId string|string[]
     *
     * @return array
     */
    public function deleteFolder($folderId): array
    {
        Helper::checkProviderUnit($this->options, false);

        $folderId    = Helper::fooToArray($folderId);
        $accessToken = $this->options->getAccessToken();

        $rule = V::simpleArray(V::stringType()->notEmpty());

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

            $queues[] = static function () use ($request, $target)
            {
                return $request->delete($target);
            };
        }

        $pools = $this->options->getAsync()->pool($queues, false);

        return Helper::concatPoolInfos($folderId, $pools);
    }

    // ------------------------------------------------------------------------------
}
