<?php

namespace Tests\Labels;

use Exception;
use Tests\AbsCase;

/**
 * ----------------------------------------------------------------------------------
 * Label Test
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2020/04/26
 *
 * @internal
 */
class LabelTest extends AbsCase
{
    // ------------------------------------------------------------------------------

    public function testGetLabelList(): void
    {
        $data = $this->client->Labels()->Label()->getLabelsList();

        $this->assertIsArray($data);
    }

    // ------------------------------------------------------------------------------

    public function testGetLabel(): void
    {
        $id = 'aenlhdgl3o55sc37a6fxjgjmo';

        $data = $this->client->Labels()->Label()->getLabel($id);

        $this->assertArrayHasKey($id, $data);
    }

    // ------------------------------------------------------------------------------

    public function testAddLabel(): void
    {
        $name = 'test_label'.\uniqid();

        $data = $this->client->Labels()->Label()->addLabel($name);

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

    public function testUpdateLabel(): void
    {
        $params =
        [
            'id'           => 'aenlhdgl3o55sc37a6fxjgjmo',
            'display_name' => 'woo'.\uniqid(),
        ];

        $data = $this->client->Labels()->Label()->updateLabel($params);

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

    public function testDeleteLabel(): void
    {
        $name = 'test_label'.\uniqid();
        $data = $this->client->Labels()->Label()->addLabel($name);

        $params['id']           = $data['id'];
        $params['display_name'] = 'wooTTT'.\uniqid();

        try
        {
            $back = true;
            $this->client->Labels()->Label()->deleteLabel($params);
        }
        catch (Exception $e)
        {
            $back = false;
        }

        $this->assertTrue($back);
    }

    // ------------------------------------------------------------------------------
}
