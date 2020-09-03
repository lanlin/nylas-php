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
 * @change 2020/04/26
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
     *
     * @return array
     */
    public function getFilesList(array $params = []): array
    {
        $accessToken = $this->options->getAccessToken();

        $rule = V::keySet(
            V::keyOptional('view', V::in(['count', 'ids'])),
            V::keyOptional('filename', V::stringType()->notEmpty()),
            V::keyOptional('message_id', V::stringType()->notEmpty()),
            V::keyOptional('content_type', V::stringType()->notEmpty())
        );

        V::doValidate($rule, $params);
        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $header = ['Authorization' => $accessToken];

        return $this->options
            ->getSync()
            ->setQuery($params)
            ->setHeaderParams($header)
            ->get(API::LIST['files']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get file infos (not download file)
     *
     * @param mixed $fileId string|string[]
     *
     * @return array
     */
    public function getFileInfo($fileId): array
    {
        $fileId      = Helper::fooToArray($fileId);
        $accessToken = $this->options->getAccessToken();

        $rule = V::simpleArray(V::stringType()->notEmpty());

        V::doValidate($rule, $fileId);
        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $queues = [];
        $target = API::LIST['oneFile'];
        $header = ['Authorization' => $accessToken];

        foreach ($fileId as $id)
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

        return Helper::concatPoolInfos($fileId, $pools);
    }

    // ------------------------------------------------------------------------------

    /**
     * delete file
     *
     * @param mixed $fileId string|string[]
     *
     * @return array
     */
    public function deleteFile($fileId): array
    {
        $fileId      = Helper::fooToArray($fileId);
        $accessToken = $this->options->getAccessToken();

        $rule = V::simpleArray(V::stringType()->notEmpty());

        V::doValidate($rule, $fileId);
        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $queues = [];
        $target = API::LIST['oneFile'];
        $header = ['Authorization' => $accessToken];

        foreach ($fileId as $id)
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

        return Helper::concatPoolInfos($fileId, $pools);
    }

    // ------------------------------------------------------------------------------

    /**
     * upload file (support multiple upload)
     *
     * @param array $file
     *
     * @return array
     */
    public function uploadFile(array $file): array
    {
        $fileUploads = Helper::arrayToMulti($file);
        $accessToken = $this->options->getAccessToken();

        V::doValidate($this->multipartRules(), $fileUploads);
        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $upload = [];
        $target = API::LIST['files'];
        $header = ['Authorization' => $accessToken];

        foreach ($fileUploads as $item)
        {
            $item['name'] = 'file';

            if (\is_string($item['contents']) && \file_exists($item['contents']))
            {
                $item['contents'] = \fopen($item['contents'], 'rb');
            }

            $request = $this->options
                ->getAsync()
                ->setFormFiles($item)
                ->setHeaderParams($header);

            $upload[] = static function () use ($request, $target)
            {
                return $request->post($target);
            };
        }

        $pools = $this->options->getAsync()->pool($upload, false);

        return $this->concatUploadInfos($fileUploads, $pools);
    }

    // ------------------------------------------------------------------------------

    /**
     * download file (support multiple download)
     *
     * @param array $params
     *
     * @return array
     */
    public function downloadFile(array $params): array
    {
        $downloadArr = Helper::arrayToMulti($params);
        $accessToken = $this->options->getAccessToken();

        V::doValidate($this->downloadRules(), $downloadArr);
        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $method = [];
        $target = API::LIST['downloadFile'];
        $header = ['Authorization' => $accessToken];

        foreach ($downloadArr as $item)
        {
            $sink = $item['path'];

            $request = $this->options
                ->getAsync()
                ->setPath($item['id'])
                ->setHeaderParams($header);

            $method[] = static function () use ($request, $target, $sink)
            {
                return $request->getSink($target, $sink);
            };
        }

        $pools = $this->options->getAsync()->pool($method, true);

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

        return  V::simpleArray(V::keySet(
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
            V::key('headers', V::arrayType(), false),
            V::key('filename', V::stringType()->length(1, null), false),
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
}
