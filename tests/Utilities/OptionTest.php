<?php

namespace Tests\Utilities;

use Nylas\Utilities\Options;
use PHPUnit\Framework\TestCase;
use Nylas\Exceptions\NylasException;

/**
 * @internal
 */
class OptionTest extends TestCase
{
    // ------------------------------------------------------------------------------

    public function testMinimalRequirementOption(): void
    {
        $optionsData = [
            'client_id'     => \uniqid(),
            'client_secret' => \uniqid(),
        ];

        $options = new Options($optionsData);

        $optionsResult = [
            'client_id'     => $options->getClientId(),
            'client_secret' => $options->getClientSecret(),
        ];

        $this->assertSame($optionsData, $optionsResult);
    }

    // ------------------------------------------------------------------------------

    public function testMissingRequirementOption(): void
    {
        $optionsData = ['client_id' => \uniqid()];

        $this->expectException(NylasException::class);

        new Options($optionsData);
    }

    // ------------------------------------------------------------------------------

    public function testOptions(): void
    {
        $optionsData = [
            'debug'         => true,
            'log_file'      => 'foo',
            'client_id'     => \uniqid(),
            'client_secret' => \uniqid(),
        ];

        $options = new Options($optionsData);

        $data = $options->getAllOptions();

        $this->assertArrayHasKey('log_file', $data);
    }

    // ------------------------------------------------------------------------------
}
