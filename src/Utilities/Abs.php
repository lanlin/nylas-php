<?php

namespace Nylas\Utilities;

use Nylas\Exceptions\NylasException;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Abs
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2021/07/21
 */
trait Abs
{
    // ------------------------------------------------------------------------------

    /**
     * @var Options
     */
    private Options $options;

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
     * call nylas apis with __get
     *
     * @param string $name
     *
     * @return object
     */
    public function __get(string $name): object
    {
        return $this->callSubClass($name);
    }

    // ------------------------------------------------------------------------------

    /**
     * call nylas apis with __call
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return object
     */
    public function __call(string $name, array $arguments): object
    {
        return $this->callSubClass($name, $arguments);
    }

    // ------------------------------------------------------------------------------

    /**
     * call sub class
     *
     * @param  string  $name
     * @param  array   $arguments
     *
     * @return object
     */
    private function callSubClass(string $name, array $arguments = []): object
    {
        $nmSpace  = \trim(\get_class($this), 'Abs');
        $nmSpace  = \trim($nmSpace, '\\');
        $subClass = $nmSpace.'\\'.\ucfirst($name);

        // check class exists
        if (!\class_exists($subClass))
        {
            throw new NylasException(null, "class {$subClass} not found!");
        }

        return new $subClass($this->options, ...$arguments);
    }

    // ------------------------------------------------------------------------------
}
