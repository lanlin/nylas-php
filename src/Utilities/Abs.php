<?php

namespace Nylas\Utilities;

use Nylas\Exceptions\NylasException;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Abs
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2020/06/22
 */
trait Abs
{
    // ------------------------------------------------------------------------------

    /**
     * @var Options
     */
    private $options;

    // ------------------------------------------------------------------------------

    /**
     * Abs constructor.
     *
     * @param \Nylas\Utilities\Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    // ------------------------------------------------------------------------------

    /**
     * call nylas apis
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return object
     */
    public function __call(string $name, array $arguments): object
    {
        $nmSpace  = \trim(\get_class($this), 'Abs');
        $nmSpace  = \trim($nmSpace, '\\');
        $subClass = $nmSpace.'\\'.\ucfirst($name);

        // check class exists
        if (!\class_exists($subClass))
        {
            throw new NylasException(null, "class {$subClass} not found!");
        }

        return new $subClass($this->options);
    }

    // ------------------------------------------------------------------------------
}
