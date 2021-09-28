<?php

namespace Tests\Files;

use Tests\AbsCase;

/**
 * ----------------------------------------------------------------------------------
 * File Test
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2021/09/22
 *
 * @internal
 */
class FileTest extends AbsCase
{
    // ------------------------------------------------------------------------------

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

        $this->assertArrayHasKey('account_id', $data[0]);
    }

    // ------------------------------------------------------------------------------

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

        $this->assertArrayHasKey('account_id', $data[0]);
    }

    // ------------------------------------------------------------------------------

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

        $this->assertArrayHasKey($id, $data);
    }

    // ------------------------------------------------------------------------------

    public function testDeleteAFile(): void
    {
        $id = '9etjh6talp***';

        $this->mockResponse([]);

        $data = $this->client->Files->File->deleteAFile($id);

        $this->assertArrayHasKey($id, $data);
    }

    // ------------------------------------------------------------------------------

    public function testDownloadAFile(): void
    {
        $file = [
            'id'   => '3ni3ak1mapl4v03wtr5k2puw0',
            'path' => __DIR__.'/a.png',
        ];

        $this->mockResponse([]);

        $data = $this->client->Files->File->downloadAFile($file);

        \unlink($file['path']);

        $this->assertArrayHasKey($file['id'], $data);
    }

    // ------------------------------------------------------------------------------
}
