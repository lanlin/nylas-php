<?php

namespace Nylas\Files;

use Nylas\Utilities\API;
use Nylas\Utilities\Helper;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validator as V;
use Psr\Http\Message\StreamInterface;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Files
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2021/09/22
 */
class File
{
    // ------------------------------------------------------------------------------

    /**
     * @var \Nylas\Utilities\Options
     */
    private Options $options;

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
     * Returns information about each files metadata.
     *
     * @see https://developer.nylas.com/docs/api/#get/files
     *
     * @param array $params
     *
     * @return array
     */
    public function returnAllFiles(array $params = []): array
    {
        V::doValidate(V::keySet(
            V::keyOptional('view', V::in(['ids', 'count', 'expanded'])),
            V::keyOptional('filename', V::stringType()->notEmpty()),
            V::keyOptional('message_id', V::stringType()->notEmpty()),
            V::keyOptional('content_type', V::in([$this->contentTypes()]))
        ), $params);

        return $this->options
            ->getSync()
            ->setQuery($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->get(API::LIST['files']);
    }

    // ------------------------------------------------------------------------------

    /**
     * Uploads a new file. Uploaded files are valid for 7 days.
     *
     * @see https://developer.nylas.com/docs/api/#post/files
     *
     * @param array $file
     *
     * @return array
     */
    public function uploadAFile(array $file): array
    {
        $fileUploads = Helper::arrayToMulti($file);

        V::doValidate($this->multipartRules(), $fileUploads);

        $upload = [];

        foreach ($fileUploads as $item)
        {
            $item['name'] ??= 'file';

            if (\is_string($item['contents']) && \file_exists($item['contents']))
            {
                $item['contents'] = \fopen($item['contents'], 'rb');
            }

            $request = $this->options
                ->getAsync()
                ->setFormFiles($item)
                ->setHeaderParams($this->options->getAuthorizationHeader());

            $upload[] = static function () use ($request)
            {
                return $request->post(API::LIST['files']);
            };
        }

        $pools = $this->options->getAsync()->pool($upload, false);

        return $this->concatUploadInfos($fileUploads, $pools);
    }

    // ------------------------------------------------------------------------------

    /**
     * Returns file metadata by ID.
     *
     * @see https://developer.nylas.com/docs/api/#get/files/id
     *
     * @param mixed $fileId string|string[]
     *
     * @return array
     */
    public function returnAFile(mixed $fileId): array
    {
        $fileId = Helper::fooToArray($fileId);

        V::doValidate(V::simpleArray(V::stringType()->notEmpty()), $fileId);

        $queues = [];

        foreach ($fileId as $id)
        {
            $request = $this->options
                ->getAsync()
                ->setPath($id)
                ->setHeaderParams($this->options->getAuthorizationHeader());

            $queues[] = static function () use ($request)
            {
                return $request->get(API::LIST['oneFile']);
            };
        }

        $pools = $this->options->getAsync()->pool($queues, false);

        return Helper::concatPoolInfos($fileId, $pools);
    }

    // ------------------------------------------------------------------------------

    /**
     * Deletes a file by ID.
     *
     * @see https://developer.nylas.com/docs/api/#delete/files/id
     *
     * @param mixed $fileId string|string[]
     *
     * @return array
     */
    public function deleteAFile(mixed $fileId): array
    {
        $fileId = Helper::fooToArray($fileId);

        V::doValidate(V::simpleArray(V::stringType()->notEmpty()), $fileId);

        $queues = [];

        foreach ($fileId as $id)
        {
            $request = $this->options
                ->getAsync()
                ->setPath($id)
                ->setHeaderParams($this->options->getAuthorizationHeader());

            $queues[] = static function () use ($request)
            {
                return $request->delete(API::LIST['oneFile']);
            };
        }

        $pools = $this->options->getAsync()->pool($queues, false);

        return Helper::concatPoolInfos($fileId, $pools);
    }

    // ------------------------------------------------------------------------------

    /**
     * Download a file.
     *
     * @see https://developer.nylas.com/docs/api/#get/files/id/download
     *
     * @param array $params
     *
     * @return array
     */
    public function downloadAFile(array $params): array
    {
        $downloadArr = Helper::arrayToMulti($params);

        V::doValidate($this->downloadRules(), $downloadArr);

        $queues = [];

        foreach ($downloadArr as $item)
        {
            $request = $this->options
                ->getAsync()
                ->setPath($item['id'])
                ->setHeaderParams($this->options->getAuthorizationHeader());

            $queues[] = static function () use ($request, $item)
            {
                return $request->getSink(API::LIST['downloadFile'], $item['path']);
            };
        }

        $pools = $this->options->getAsync()->pool($queues, true);

        return $this->concatDownloadInfos($downloadArr, $pools);
    }

    // ------------------------------------------------------------------------------

    /**
     * rules for download params
     *
     * @return \Nylas\Utilities\Validator
     */
    private function downloadRules(): V
    {
        $path = V::oneOf(
            V::resourceType(),
            V::stringType()->notEmpty(),
            V::instance(StreamInterface::class)
        );

        return V::simpleArray(V::keySet(
            V::key('id', V::stringType()->notEmpty()),
            V::key('path', $path)
        ));
    }

    // ------------------------------------------------------------------------------

    /**
     * multipart upload rules
     *
     * @return \Nylas\Utilities\Validator
     */
    private function multipartRules(): V
    {
        return V::simpleArray(V::keyset(
            V::key('name', V::stringType()->notEmpty(), false),
            V::key('headers', V::arrayType(), false),
            V::key('filename', V::stringType()->notEmpty(), false),
            V::key('contents', V::oneOf(
                V::resourceType(),
                V::stringType()->notEmpty(),
                V::instance(StreamInterface::class)
            ))
        ));
    }

    // ------------------------------------------------------------------------------

    /**
     * concat upload infos
     *
     * @param array $files
     * @param array $pools
     *
     * @return array
     */
    private function concatUploadInfos(array $files, array $pools): array
    {
        foreach ($files as $index => &$item)
        {
            if (isset($pools[$index]['error']))
            {
                $item = \array_merge($item, $pools[$index]);
            }

            if (isset($pools[$index][0]))
            {
                $item = \array_merge($item, $pools[$index][0]);
            }
        }

        unset($item);

        return $files;
    }

    // ------------------------------------------------------------------------------

    /**
     * concat download infos
     *
     * @param array $params
     * @param array $pools
     *
     * @return array
     */
    private function concatDownloadInfos(array $params, array $pools): array
    {
        $data = [];

        foreach ($params as $index => $item)
        {
            if (isset($pools[$index]['error']))
            {
                $item = \array_merge($item, $pools[$index]);
            }

            if (isset($pools[$index]['Content-Disposition'][0]))
            {
                $str = $pools[$index]['Content-Disposition'][0];

                $item['size']     = $pools[$index]['Content-Length'][0];
                $item['filename'] = \str_replace('attachment; filename=', '', $str);
            }

            $data[$item['id']] = $item;
        }

        return $data;
    }

    // ------------------------------------------------------------------------------

    /**
     * @return string[]
     */
    private function contentTypes(): array
    {
        return [
            'text/html',
            'text/plain',

            'image/png',
            'image/gif',
            'image/jpg',
            'image/jpeg',

            'message/rfc822',
            'multipart/mixed',
            'multipart/signed',
            'multipart/related',
            'multipart/alternative',

            'application/pdf',
            'application/msword',
            'application/octet-stream',
            'application/pkcs7-signature',
        ];
    }

    // ------------------------------------------------------------------------------
}
