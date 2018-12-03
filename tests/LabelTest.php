<?php namespace NylasTest;

/**
 * ----------------------------------------------------------------------------------
 * Label Test
 * ----------------------------------------------------------------------------------
 *
 * @update lanlin
 * @change 2018/12/03
 */
class LabelTest extends Abs
{

    // ------------------------------------------------------------------------------

    public function testGetLabelList()
    {
        $data = self::$api->Labels()->Label()->getLabelsList();

        $this->assertTrue(count($data) > 0);
    }

    // ------------------------------------------------------------------------------

    public function testGetLabel()
    {
        $id = 'aenlhdgl3o55sc37a6fxjgjmo';

        $data = self::$api->Labels()->Label()->getLabel($id);

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

    public function testAddLabel()
    {
        $name = 'test_label' . uniqid();

        $data = self::$api->Labels()->Label()->addLabel($name);

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

    public function testUpdateLabel()
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

    public function testDeleteLabel()
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
