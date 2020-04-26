<?php namespace NylasTest;


/**
 * ----------------------------------------------------------------------------------
 * Folder Test
 * ----------------------------------------------------------------------------------
 *
 * @update lanlin
 * @change 2020/04/26
 */
class FolderTest extends Abs
{

    // ------------------------------------------------------------------------------

    public function testGetFolderList() : void
    {
        $data = self::$api->Folders()->Folder()->getFoldersList();

        $this->assertTrue(count($data) > 0);
    }

    // ------------------------------------------------------------------------------

    public function testGetFolder() : void
    {
        $id = 'ejom4k3o5qor5ooyh8yx7hgbw';

        $data = self::$api->Folders()->Folder()->getFolder($id);

        $this->assertArrayHasKey($id, $data);
    }

    // ------------------------------------------------------------------------------

    public function testAddFolder() : void
    {
        $name = 'test_folder'.uniqid();

        $data = self::$api->Folders()->Folder()->addFolder($name);

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

    public function testUpdateFolder() : void
    {
        $params =
        [
            'id'           => '47137b6urkg0cf738o7is2aa3',
            'display_name' => 'woo---'
        ];

        $data = self::$api->Folders()->Folder()->updateFolder($params);

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

    public function testDeleteFolder() : void
    {
        $id = '47137b6urkg0cf738o7is2aa3';

        try
        {
            $back = true;
            self::$api->Folders()->Folder()->deleteFolder($id);
        }
        catch (\Exception $e)
        {
            $back = false;
        }

        $this->assertTrue($back);
    }

    // ------------------------------------------------------------------------------

}
