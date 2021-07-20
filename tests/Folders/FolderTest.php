<?php

namespace Tests\Folders;

use Exception;
use Tests\AbsCase;

/**
 * ----------------------------------------------------------------------------------
 * Folder Test
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2020/04/26
 *
 * @internal
 */
class FolderTest extends AbsCase
{
    // ------------------------------------------------------------------------------

    public function testGetFolderList(): void
    {
        $data = $this->client->Folders()->Folder()->getFoldersList();

        $this->assertIsArray($data);
    }

    // ------------------------------------------------------------------------------

    public function testGetFolder(): void
    {
        $id = 'ejom4k3o5qor5ooyh8yx7hgbw';

        $data = $this->client->Folders()->Folder()->getFolder($id);

        $this->assertArrayHasKey($id, $data);
    }

    // ------------------------------------------------------------------------------

    public function testAddFolder(): void
    {
        $name = 'test_folder'.\uniqid();

        $data = $this->client->Folders()->Folder()->addFolder($name);

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

    public function testUpdateFolder(): void
    {
        $params =
        [
            'id'           => '47137b6urkg0cf738o7is2aa3',
            'display_name' => 'woo---',
        ];

        $data = $this->client->Folders()->Folder()->updateFolder($params);

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

    public function testDeleteFolder(): void
    {
        $id = '47137b6urkg0cf738o7is2aa3';

        try
        {
            $back = true;
            $this->client->Folders()->Folder()->deleteFolder($id);
        }
        catch (Exception $e)
        {
            $back = false;
        }

        $this->assertTrue($back);
    }

    // ------------------------------------------------------------------------------
}
