<?php

namespace NylasTest;

use Nylas\Exceptions\NylasException;
use Nylas\Utilities\Options;
use PHPUnit\Framework\TestCase;

class OptionTest extends TestCase
{
	public function testMinimalRequirementOption()
	{
		$optionsData = [
			'client_id' => uniqid(),
			'client_secret' => uniqid(),
		];

		$options = new Options($optionsData);
		$this->assertSame($optionsData, $options->getClientApps());
	}

    public function testMissingRequirementOption()
    {
        $optionsData = [
            'client_id' => uniqid(),
        ];

        $this->expectException(NylasException::class);
        new Options($optionsData);
    }

    public function testOptions()
    {
        $optionsData = [
            'debug' => true,
            'log_file' => 'foo',
            'client_id' => uniqid(),
            'client_secret' => uniqid(),
            'account_id' => uniqid(),
            'off_decode_error' => true,
        ];

        $options = new Options($optionsData);
        $this->assertSame($optionsData['off_decode_error'], $options->getOffDecodeError());
        $this->assertSame($optionsData['account_id'], $options->getAccountId());
        $data = $options->getAllOptions();
        unset($data['server']);
        unset($data['access_token']);
        $this->assertSame($optionsData, $data);
    }
}
