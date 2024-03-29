<?php

declare(strict_types = 1);

namespace Nylas\Folders;

use Nylas\Utilities\API;
use Nylas\Utilities\Helper;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validator as V;
use GuzzleHttp\Exception\GuzzleException;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Folders
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2023/07/21
 */
class Folder
{
    // ------------------------------------------------------------------------------

    /**
     * @var Options
     */
    private Options $options;

    // ------------------------------------------------------------------------------

    /**
     * Folder constructor.
     *
     * @param Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    // ------------------------------------------------------------------------------

    /**
     * Returns all folders.
     *
     * @see https://developer.nylas.com/docs/api/v2/#get-/folders
     *
     * @param array $params
     *
     * @return array
     * @throws GuzzleException
     */
    public function returnAllFolders(array $params = []): array
    {
        V::doValidate(V::keySet(
            V::keyOptional('limit', V::intType()::min(1)),
            V::keyOptional('offset', V::intType()::min(0)),
            V::keyOptional('view', V::in(['ids', 'count'])),
        ), $params);

        return $this->options
            ->getSync()
            ->setQuery($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->get(API::LIST['folders']);
    }

    // ------------------------------------------------------------------------------

    /**
     * Creates a new folder.
     *
     * @see https://developer.nylas.com/docs/api/v2/#post-/folders
     *
     * @param array $params
     *
     * @return array
     * @throws GuzzleException
     */
    public function createAFolder(array $params = []): array
    {
        V::doValidate(V::keySet(
            V::keyOptional('name', V::stringType()::notEmpty()),
            V::keyOptional('display_name', V::stringType()::notEmpty()),
        ), $params);

        return $this->options
            ->getSync()
            ->setFormParams($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->post(API::LIST['folders']);
    }

    // ------------------------------------------------------------------------------

    /**
     * Returns a folder by ID.
     *
     * @see https://developer.nylas.com/docs/api/v2/#get-/folders/id
     *
     * @param mixed $folderId string|string[]
     *
     * @return array
     */
    public function returnAFolder(mixed $folderId): array
    {
        $folderId = Helper::fooToArray($folderId);

        V::doValidate(V::simpleArray(V::stringType()::notEmpty()), $folderId);

        $queues = [];

        foreach ($folderId as $id)
        {
            $request = $this->options
                ->getAsync()
                ->setPath($id)
                ->setHeaderParams($this->options->getAuthorizationHeader());

            $queues[] = static function () use ($request)
            {
                return $request->get(API::LIST['oneFolder']);
            };
        }

        $pools = $this->options->getAsync()->pool($queues, false);

        return Helper::concatPoolInfos($folderId, $pools);
    }

    // ------------------------------------------------------------------------------

    /**
     * Updates a folder by ID.
     *
     * @see https://developer.nylas.com/docs/api/v2/#put-/folders/id
     *
     * @param string $folderId
     * @param array  $params
     *
     * @return array
     * @throws GuzzleException
     */
    public function updateAFolder(string $folderId, array $params = []): array
    {
        V::doValidate(V::stringType()::notEmpty(), $folderId);

        V::doValidate(V::keySet(
            V::keyOptional('name', V::stringType()::notEmpty()),
            V::keyOptional('display_name', V::stringType()::notEmpty())
        ), $params);

        return $this->options
            ->getSync()
            ->setPath($folderId)
            ->setFormParams($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->put(API::LIST['oneFolder']);
    }

    // ------------------------------------------------------------------------------

    /**
     * Deletes a folder.
     *
     * @see https://developer.nylas.com/docs/api/v2/#delete-/folders/id
     *
     * @param mixed $folderId string|string[]
     *
     * @return array
     */
    public function deleteAFolder(mixed $folderId): array
    {
        $folderId = Helper::fooToArray($folderId);

        V::doValidate(V::simpleArray(V::stringType()::notEmpty()), $folderId);

        $queues = [];

        foreach ($folderId as $id)
        {
            $request = $this->options
                ->getAsync()
                ->setPath($id)
                ->setHeaderParams($this->options->getAuthorizationHeader());

            $queues[] = static function () use ($request)
            {
                return $request->delete(API::LIST['oneFolder']);
            };
        }

        $pools = $this->options->getAsync()->pool($queues, false);

        return Helper::concatPoolInfos($folderId, $pools);
    }

    // ------------------------------------------------------------------------------
}
