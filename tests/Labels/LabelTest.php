<?php

declare(strict_types = 1);

namespace Tests\Labels;

use JsonException;
use Tests\AbsCase;
use GuzzleHttp\Exception\GuzzleException;

/**
 * ----------------------------------------------------------------------------------
 * Label Test
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2023/07/21
 *
 * @internal
 */
class LabelTest extends AbsCase
{
    // ------------------------------------------------------------------------------

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function testReturnAllLabels(): void
    {
        $this->mockResponse([$this->getLabelData()]);

        $data = $this->client->Labels->Label->returnAllLabels();

        static::assertArrayHasKey('account_id', $data[0]);
    }

    // ------------------------------------------------------------------------------

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function testCreateALabel(): void
    {
        $name = 'My Renamed Label';

        $this->mockResponse($this->getLabelData());

        $data = $this->client->Labels->Label->createALabel($name);

        static::assertArrayHasKey('account_id', $data);
    }

    // ------------------------------------------------------------------------------

    /**
     * @throws JsonException
     */
    public function testReturnALabel(): void
    {
        $id = '12r72ur7rojisrmjp5xzau8xs';

        $this->mockResponse([$this->getLabelData()]);

        $data = $this->client->Labels->Label->returnALabel($id);

        static::assertArrayHasKey($id, $data);
    }

    // ------------------------------------------------------------------------------

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function testUpdateALabel(): void
    {
        $id   = '12r72ur7rojisrmjp5xzau8xs';
        $name = 'My Renamed Label';

        $this->mockResponse($this->getLabelData());

        $data = $this->client->Labels->Label->updateALabel($id, $name);

        static::assertArrayHasKey('account_id', $data);
    }

    // ------------------------------------------------------------------------------

    /**
     * @throws JsonException
     */
    public function testDeleteALabel(): void
    {
        $id = '12r72ur7rojisrmjp5xzau8xs';

        $this->mockResponse([]);

        $data = $this->client->Labels->Label->deleteALabel($id);

        static::assertArrayHasKey($id, $data);
    }

    // ------------------------------------------------------------------------------

    /**
     * @return string[]
     */
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
