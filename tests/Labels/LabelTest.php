<?php

namespace Tests\Labels;

use Tests\AbsCase;

/**
 * ----------------------------------------------------------------------------------
 * Label Test
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2021/09/22
 *
 * @internal
 */
class LabelTest extends AbsCase
{
    // ------------------------------------------------------------------------------

    public function testReturnAllLabels(): void
    {
        $this->mockResponse([$this->getLabelData()]);

        $data = $this->client->Labels->Label->returnAllLabels();

        $this->assertArrayHasKey('account_id', $data[0]);
    }

    // ------------------------------------------------------------------------------

    public function testCreateALabel(): void
    {
        $name = 'My Renamed Label';

        $this->mockResponse($this->getLabelData());

        $data = $this->client->Labels->Label->createALabel($name);

        $this->assertArrayHasKey('account_id', $data);
    }

    // ------------------------------------------------------------------------------

    public function testReturnALabel(): void
    {
        $id = '12r72ur7rojisrmjp5xzau8xs';

        $this->mockResponse([$this->getLabelData()]);

        $data = $this->client->Labels->Label->returnALabel($id);

        $this->assertArrayHasKey($id, $data);
    }

    // ------------------------------------------------------------------------------

    public function testUpdateALabel(): void
    {
        $id   = '12r72ur7rojisrmjp5xzau8xs';
        $name = 'My Renamed Label';

        $this->mockResponse($this->getLabelData());

        $data = $this->client->Labels->Label->updateALabel($id, $name);

        $this->assertArrayHasKey('account_id', $data);
    }

    // ------------------------------------------------------------------------------

    public function testDeleteALabel(): void
    {
        $id = '12r72ur7rojisrmjp5xzau8xs';

        $this->mockResponse([]);

        $data = $this->client->Labels->Label->deleteALabel($id);

        $this->assertArrayHasKey($id, $data);
    }

    // ------------------------------------------------------------------------------

    private function getLabelData(): array
    {
        return [

            'account_id'   => 'aaz875kwuvxik6ku7pwkqp3ah',
            'display_name' => 'All Mail',
            'id'           => '12r72ur7rojisrmjp5xzau8xs',
            'name'         => 'all',
            'object'       => 'label',
        ];
    }

    // ------------------------------------------------------------------------------
}
