<?php

declare(strict_types = 1);

namespace Tests\Folders;

use JsonException;
use Tests\AbsCase;
use GuzzleHttp\Exception\GuzzleException;

/**
 * ----------------------------------------------------------------------------------
 * Folder Test
 * ----------------------------------------------------------------------------------
 *
 * @see https://developer.nylas.com/docs/api/#tag--Folders
 *
 * @author lanlin
 * @change 2023/07/21
 *
 * @internal
 */
class FolderTest extends AbsCase
{
    // ------------------------------------------------------------------------------

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function testReturnAllFolders(): void
    {
        $this->mockResponse([$this->getFolderData()]);

        $data = $this->client->Folders->Folder->returnAllFolders();

        static::assertArrayHasKey('account_id', $data[0]);
    }

    // ------------------------------------------------------------------------------

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function testCreateAFolder(): void
    {
        $params = ['display_name' => 'My Renamed Folder'];

        $this->mockResponse($this->getFolderData());

        $data = $this->client->Folders->Folder->createAFolder($params);

        static::assertArrayHasKey('account_id', $data);
    }

    // ------------------------------------------------------------------------------

    /**
     * @throws JsonException
     */
    public function testReturnAFolder(): void
    {
        $id = 'ajs4ef7xu74vns6o5ufsu69m7';

        $this->mockResponse([$this->getFolderData()]);

        $data = $this->client->Folders->Folder->returnAFolder($id);

        static::assertArrayHasKey($id, $data);
    }

    // ------------------------------------------------------------------------------

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function testUpdateAFolder(): void
    {
        $id     = 'ajs4ef7xu74vns6o5ufsu69m7';
        $params = ['display_name' => 'My Renamed Folder'];

        $this->mockResponse($this->getFolderData());

        $data = $this->client->Folders->Folder->updateAFolder($id, $params);

        static::assertArrayHasKey('account_id', $data);
    }

    // ------------------------------------------------------------------------------

    /**
     * @throws JsonException
     */
    public function testDeleteAFolder(): void
    {
        $id = 'ajs4ef7xu74vns6o5ufsu69m7';

        $this->mockResponse([]);

        $data = $this->client->Folders->Folder->deleteAFolder($id);

        static::assertArrayHasKey($id, $data);
    }

    // ------------------------------------------------------------------------------

    /**
     * @return string[]
     */
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
