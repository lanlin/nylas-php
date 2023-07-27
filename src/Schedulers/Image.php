<?php

declare(strict_types = 1);

namespace Nylas\Schedulers;

use function fopen;
use function is_string;
use function file_exists;

use RuntimeException;
use Nylas\Utilities\API;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validator as V;
use Psr\Http\Message\StreamInterface;
use GuzzleHttp\Exception\GuzzleException;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Scheduler Image Upload
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2023/07/24
 */
class Image
{
    // ------------------------------------------------------------------------------

    /**
     * @var Options
     */
    private Options $options;

    // ------------------------------------------------------------------------------

    /**
     * Scheduler constructor.
     *
     * @param Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
        $this->options->setSchedulerServer($this->options->getRegion());
    }

    // ------------------------------------------------------------------------------

    /**
     * Upload and image. Retrieve a pre-signed S3 URL for uploading.
     *
     * @param string $pageId
     * @param array  $params
     *
     * @return array
     * @throws GuzzleException
     */
    public function getPreSignedUrl(string $pageId, array $params): array
    {
        $rules = V::keySet(
            V::key('objectName', V::stringType()::notEmpty()),
            V::key('contentType', V::stringType()::notEmpty()),
        );

        V::doValidate($rules, $params);

        return $this->options
            ->getSync()
            ->setPath($pageId)
            ->setFormParams($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->put(API::LIST['schedulerUploadImg']);
    }

    // ------------------------------------------------------------------------------

    /**
     * Upload an image (same as file upload)
     *
     * @param string $pageId
     * @param array  $params
     * @param array  $image  when empty, means upload from $_FILES['image']
     *
     * tips: when $image is empty, use $_FILES['image']
     * <form action="upload.php" method="post" enctype="multipart/form-data">
     *     <input name="image" type="file" />
     *     <input type="submit" value="Send files" />
     * </form>
     *
     * @return array
     * @throws GuzzleException
     */
    public function uploadAnImage(string $pageId, array $params, array $image = []): array
    {
        if (empty($image))
        {
            $image = $this->concatUploadImage();
        }

        V::doValidate($this->multipartRules(), $image);

        $image['name'] ??= 'file';

        if (is_string($image['contents']) && file_exists($image['contents']))
        {
            $image['contents'] = fopen($image['contents'], 'rb');
        }

        $signed = $this->getPreSignedUrl($pageId, $params);

        return $this->options
            ->getSync()
            ->setFormFiles($image)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->post($signed['signedUrl']);
    }

    // ------------------------------------------------------------------------------

    /**
     * multipart upload rules
     *
     * @return V
     */
    private function multipartRules(): V
    {
        return V::keySet(
            V::key('name', V::stringType()::notEmpty(), false),
            V::key('headers', V::arrayType(), false),
            V::key('filename', V::stringType()::notEmpty(), false),
            V::key('contents', V::oneOf(
                V::resourceType(),
                V::stringType()::notEmpty(),
                V::instance(StreamInterface::class)
            ))
        );
    }

    // ------------------------------------------------------------------------------

    /**
     * @return array
     */
    private function concatUploadImage(): array
    {
        if (empty($_FILES['image']) || empty($_FILES['image']['tmp_name']))
        {
            throw new RuntimeException('None upload image found');
        }

        return [
            'name'     => 'file',
            'headers'  => [],
            'filename' => $_FILES['image']['name'],
            'contents' => $_FILES['image']['tmp_name'],
        ];
    }

    // ------------------------------------------------------------------------------
}
