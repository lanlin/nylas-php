<?php

namespace NylasTest;

use Nylas\Utilities\Options;
use PHPUnit\Framework\TestCase;
use Nylas\Exceptions\NylasException;

/**
 * @internal
 */
class OptionTest extends TestCase
{
    public function testMinimalRequirementOption(): void
    {
        $optionsData = [
            'client_id'     => \uniqid(),
            'client_secret' => \uniqid(),
        ];

        $options = new Options($optionsData);
        $this->assertSame($optionsData, $options->getClientApps());
    }

    public function testMissingRequirementOption(): void
    {
        $optionsData = [
            'client_id' => \uniqid(),
        ];

        $this->expectException(NylasException::class);
        new Options($optionsData);
    }

    public function testOptions(): void
    {
        $optionsData = [
            'debug'            => true,
            'log_file'         => 'foo',
            'client_id'        => \uniqid(),
            'client_secret'    => \uniqid(),
            'account_id'       => \uniqid(),
            'off_decode_error' => true,
        ];

        $options = new Options($optionsData);
        $this->assertSame($optionsData['off_decode_error'], $options->getOffDecodeError());
        $this->assertSame($optionsData['account_id'], $options->getAccountId());
        $data = $options->getAllOptions();
        unset($data['server'], $data['access_token']);

        $this->assertSame($optionsData, $data);
    }
}
