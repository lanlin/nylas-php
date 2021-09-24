<?php

namespace Nylas\Neural;

use Nylas\Utilities\API;
use Nylas\Utilities\Helper;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validator as V;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Neural
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2021/09/24
 */
class Categorize
{
    // ------------------------------------------------------------------------------

    /**
     * @var \Nylas\Utilities\Options
     */
    private Options $options;

    // ------------------------------------------------------------------------------

    /**
     * Message constructor.
     *
     * @param \Nylas\Utilities\Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    // ------------------------------------------------------------------------------
}
