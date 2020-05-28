<?php namespace NylasTest;

/**
 * ----------------------------------------------------------------------------------
 * Label Test
 * ----------------------------------------------------------------------------------
 *
 * @update lanlin
 * @change 2020/04/26
 */
class LabelTest extends Abs
{

    // ------------------------------------------------------------------------------

    public function testGetLabelList() : void
    {
        $data = self::$api->Labels()->Label()->getLabelsList();

        $this->assertTrue(count($data) > 0);
    }

    // ------------------------------------------------------------------------------

    public function testGetLabel() : void
    {
        $id = 'aenlhdgl3o55sc37a6fxjgjmo';

        $data = self::$api->Labels()->Label()->getLabel($id);

        $this->assertArrayHasKey($id, $data);
    }

    // ------------------------------------------------------------------------------

    public function testAddLabel() : void
    {
        $name = 'test_label' . uniqid();

        $data = self::$api->Labels()->Label()->addLabel($name);

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

    public function testUpdateLabel() : void
    {
        $params =
        [
            'id'           => 'aenlhdgl3o55sc37a6fxjgjmo',
            'display_name' => 'woo' . uniqid()
        ];

        $data = self::$api->Labels()->Label()->updateLabel($params);

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

    public function testDeleteLabel() : void
    {

        $name = 'test_label' . uniqid();
        $data = self::$api->Labels()->Label()->addLabel($name);

        $params['id'] = $data['id'];
        $params['display_name'] = 'wooTTT' . uniqid();

        try
        {
            $back = true;
            self::$api->Labels()->Label()->deleteLabel($params);
        }
        catch (\Exception $e)
        {
            $back = false;
        }

        $this->assertTrue($back);
    }

    // ------------------------------------------------------------------------------

}
