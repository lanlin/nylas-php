<?php

namespace Tests\Folders;

use Tests\AbsCase;

/**
 * ----------------------------------------------------------------------------------
 * Folder Test
 * ----------------------------------------------------------------------------------
 *
 * @see https://developer.nylas.com/docs/api/#tag--Folders
 *
 * @author lanlin
 * @change 2021/09/22
 *
 * @internal
 */
class FolderTest extends AbsCase
{
    // ------------------------------------------------------------------------------

    public function testReturnAllFolders(): void
    {
        $this->mockResponse([$this->getFolderData()]);

        $data = $this->client->Folders->Folder->returnAllFolders();

        $this->assertArrayHasKey('account_id', $data[0]);
    }

    // ------------------------------------------------------------------------------

    public function testCreateAFolder(): void
    {
        $params = ['display_name' => 'My Renamed Folder'];

        $this->mockResponse($this->getFolderData());

        $data = $this->client->Folders->Folder->createAFolder($params);

        $this->assertArrayHasKey('account_id', $data);
    }

    // ------------------------------------------------------------------------------

    public function testReturnAFolder(): void
    {
        $id = 'ajs4ef7xu74vns6o5ufsu69m7';

        $this->mockResponse([$this->getFolderData()]);

        $data = $this->client->Folders->Folder->returnAFolder($id);

        $this->assertArrayHasKey($id, $data);
    }

    // ------------------------------------------------------------------------------

    public function testUpdateAFolder(): void
    {
        $id     = 'ajs4ef7xu74vns6o5ufsu69m7';
        $params = ['display_name' => 'My Renamed Folder'];

        $this->mockResponse($this->getFolderData());

        $data = $this->client->Folders->Folder->updateAFolder($id, $params);

        $this->assertArrayHasKey('account_id', $data);
    }

    // ------------------------------------------------------------------------------

    public function testDeleteAFolder(): void
    {
        $id = 'ajs4ef7xu74vns6o5ufsu69m7';

        $this->mockResponse([]);

        $data = $this->client->Folders->Folder->deleteAFolder($id);

        $this->assertArrayHasKey($id, $data);
    }

    // ------------------------------------------------------------------------------

    private function getFolderData(): array
    {
        return [
            'account_id'   => '79xcak1h10r1tmm5ogavx28lb',
            'display_name' => 'Archive',
            'id'           => 'ajs4ef7xu74vns6o5ufsu69m7',
            'name'         => 'archive',
            'object'       => 'folder',
        ];
    }

    // ------------------------------------------------------------------------------
}
