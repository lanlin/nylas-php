<?php namespace NylasTest;

/**
 * ----------------------------------------------------------------------------------
 * File Test
 * ----------------------------------------------------------------------------------
 *
 * @update lanlin
 * @change 2020/04/26
 */
class FileTest extends Abs
{

    // ------------------------------------------------------------------------------

    public function testGetFileList() : void
    {
        $data = self::$api->Files()->File()->getFilesList();

        $this->assertTrue(count($data) > 0);
    }

    // ------------------------------------------------------------------------------

    public function testGetFile() : void
    {
        $id = '6i1hjmlao8s2b5oi7fsntq9va';

        $data = self::$api->Files()->File()->getFileInfo($id);

        $this->assertArrayHasKey($id, $data);
    }

    // ------------------------------------------------------------------------------

    public function testUploadFile() : void
    {
        $file[] =
        [
            'contents' => __DIR__. '/correct.png',
            'filename' => 'test_correct.png'
        ];

        $file[] =
        [
            'contents' => __DIR__. '/clound.png',
            'filename' => 'test_clound.png'
        ];

        $data = self::$api->Files()->File()->uploadFile($file);

        $this->assertTrue(count($data) > 0);
    }

    // ------------------------------------------------------------------------------

    public function testDownloadFile() : void
    {
        $file[] =
        [
            'id'   => '3ni3ak1mapl4v03wtr5k2puw0',
            'path' => __DIR__. '/a.png',
        ];

        $file[] =
        [
            'id'   => 'a4xl9ru0vfitmc1dbrij43yyk',
            'path' => __DIR__. '/b.png',
        ];

        $data = self::$api->Files()->File()->downloadFile($file);

        $this->assertTrue(count($data) > 0);
    }

    // ------------------------------------------------------------------------------

}
