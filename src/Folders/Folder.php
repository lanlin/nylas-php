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
 * @change 2021/09/22
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
     * Returns all folders.
     *
     * @see https://developer.nylas.com/docs/api/#get/folders
     *
     * @param int    $offset zero base
     * @param int    $limit  default 100
     * @param string $view   ids|count
     *
     * @return array
     */
    public function returnAllFolders(int $offset = 0, int $limit = 100, ?string $view = null): array
    {
        Helper::checkProviderUnit($this->options, false);

        $params = [
            'view'   => $view,
            'limit'  => $limit,
            'offset' => $offset,
        ];

        V::doValidate(V::keySet(
            V::key('limit', V::intType()),
            V::key('offset', V::intType()),
            V::keyOptional('view', V::in(['ids', 'count'])),
        ), $params);

        if (empty($params['view']))
        {
            unset($params['view']);
        }

        return $this->options
            ->getSync()
            ->setQuery($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
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
    public function createAFolder(?string $displayName = null, ?string $name = null): array
    {
        Helper::checkProviderUnit($this->options, false);

        $params = ['name' => $name, 'display_name' => $displayName];

        if (empty($name))
        {
            unset($params['name']);
        }

        if (empty($displayName))
        {
            unset($params['display_name']);
        }

        return $this->options
            ->getSync()
            ->setFormParams($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
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

        V::doValidate(V::keySet(
            V::key('id', V::stringType()->notEmpty()),
            V::keyOptional('display_name', V::stringType()->notEmpty())
        ), $params);

        $path = $params['id'];

        unset($params['id']);

        return $this->options
            ->getSync()
            ->setPath($path)
            ->setFormParams($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
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
    public function getFolder(mixed $folderId): array
    {
        Helper::checkProviderUnit($this->options, false);

        $folderId = Helper::fooToArray($folderId);

        V::doValidate(V::simpleArray(V::stringType()->notEmpty()), $folderId);

        $queues = [];
        $target = API::LIST['oneFolder'];

        foreach ($folderId as $id)
        {
            $request = $this->options
                ->getAsync()
                ->setPath($id)
                ->setHeaderParams($this->options->getAuthorizationHeader());

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
    public function deleteFolder(mixed $folderId): array
    {
        Helper::checkProviderUnit($this->options, false);

        $folderId = Helper::fooToArray($folderId);

        V::doValidate(V::simpleArray(V::stringType()->notEmpty()), $folderId);

        $queues = [];
        $target = API::LIST['oneFolder'];

        foreach ($folderId as $id)
        {
            $request = $this->options
                ->getAsync()
                ->setPath($id)
                ->setHeaderParams($this->options->getAuthorizationHeader());

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
