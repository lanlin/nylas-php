<?php

declare(strict_types = 1);

namespace Tests\Files;

use function unlink;

use JsonException;
use Tests\AbsCase;
use GuzzleHttp\Exception\GuzzleException;

/**
 * ----------------------------------------------------------------------------------
 * File Test
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2023/07/21
 *
 * @internal
 */
class FileTest extends AbsCase
{
    // ------------------------------------------------------------------------------

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function testReturnAllFiles(): void
    {
        $this->mockResponse([[
            'account_id'   => '43jf3n4es3***',
            'content_type' => 'image/jpeg',
            'filename'     => 'image.jpg',
            'id'           => '9etjh6talp***',
            'object'       => 'file',
            'size'         => 72379,
        ]]);

        $data = $this->client->Files->File->returnAllFiles();

        static::assertArrayHasKey('account_id', $data[0]);
    }

    // ------------------------------------------------------------------------------

    /**
     * @throws JsonException
     */
    public function testUploadAFile(): void
    {
        $file = [
            'contents' => __DIR__.'/correct.png',
            'filename' => 'test_correct.png',
        ];

        $this->mockResponse([[
            'account_id'   => '43jf3n4es3***',
            'content_type' => 'image/jpeg',
            'filename'     => 'image.jpg',
            'id'           => '9etjh6talp***',
            'object'       => 'file',
            'size'         => 72379,
        ]]);

        $data = $this->client->Files->File->uploadAFile($file);

        static::assertArrayHasKey('account_id', $data[0]);
    }

    // ------------------------------------------------------------------------------

    /**
     * @throws JsonException
     */
    public function testReturnAFile(): void
    {
        $id = '9etjh6talp***';

        $this->mockResponse([[
            'account_id'   => '43jf3n4es3***',
            'content_type' => 'image/jpeg',
            'filename'     => 'image.jpg',
            'id'           => '9etjh6talp***',
            'object'       => 'file',
            'size'         => 72379,
        ]]);

        $data = $this->client->Files->File->returnAFile($id);

        static::assertArrayHasKey($id, $data);
    }

    // ------------------------------------------------------------------------------

    /**
     * @throws JsonException
     */
    public function testDeleteAFile(): void
    {
        $id = '9etjh6talp***';

        $this->mockResponse([]);

        $data = $this->client->Files->File->deleteAFile($id);

        static::assertArrayHasKey($id, $data);
    }

    // ------------------------------------------------------------------------------

    /**
     * @throws JsonException
     */
    public function testDownloadAFile(): void
    {
        $file = [
            'id'   => '3ni3ak1mapl4v03wtr5k2puw0',
            'path' => __DIR__.'/a.png',
        ];

        $this->mockResponse([]);

        $data = $this->client->Files->File->downloadAFile($file);

        unlink($file['path']);

        static::assertArrayHasKey($file['id'], $data);
    }

    // ------------------------------------------------------------------------------
}
