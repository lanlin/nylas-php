<?php namespace Nylas;

use Nylas\Utilities\Options;
use Nylas\Exceptions\NylasException;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Client
 * ----------------------------------------------------------------------------------
 *
 * @method Accounts\Abs Accounts()
 * @method Authentication\Abs Authentication()
 * @method Calendars\Abs Calendars()
 * @method Contacts\Abs Contacts()
 * @method Deltas\Abs Deltas()
 * @method Drafts\Abs Drafts()
 * @method Events\Abs Events()
 * @method Files\Abs Files()
 * @method Folders\Abs Folders()
 * @method Labels\Abs Labels()
 * @method Messages\Abs Messages()
 * @method Threads\Abs Threads()
 * @method Webhooks\Abs Webhooks()
 *
 * @author lanlin
 * @change 2018/11/26
 */
class Client
{

    // ------------------------------------------------------------------------------

    /**
     * @var Options
     */
    private $options;

    // ------------------------------------------------------------------------------

    /**
     * Client constructor.
     *
     * @param array $options
     * [
     *     'debug'            => bool,
     *     'log_file'         => 'log file path',
     *     'account_id'       => '',
     *     'access_token'     => '',
     *     'client_id'        => 'required',
     *     'client_secret'    => 'required',
     *     'off_decode_error' => bool,
     * ]
     */
    public function __construct(array $options)
    {
        $this->options = new Options($options);
    }

    // ------------------------------------------------------------------------------

    /**
     * get options instance for setting options
     *
     * @return \Nylas\Utilities\Options
     */
    public function Options()
    {
        return $this->options;
    }

    // ------------------------------------------------------------------------------

    /**
     * call nylas apis
     *
     * @param string $name
     * @param array $arguments
     * @return object
     */
    public function __call(string $name, array $arguments)
    {
        $apiClass = __NAMESPACE__ .'\\'. ucfirst($name) . '\\Abs';

        // check class exists
        if (!class_exists($apiClass))
        {
            throw new NylasException("class {$apiClass} not found!");
        }

        return new $apiClass($this->options);
    }

    // ------------------------------------------------------------------------------

}
