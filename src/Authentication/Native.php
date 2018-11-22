<?php namespace Nylas\Authentication;

use Nylas\Utilities\API;
use Nylas\Utilities\Request;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Native Authentication
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/11/21
 */
class Native
{

    // ------------------------------------------------------------------------------

    /**
     * @var Request
     */
    private $request;

    // ------------------------------------------------------------------------------

    /**
     * Hosted constructor.
     */
    public function __construct()
    {
        $this->request = new Request();
    }

    // ------------------------------------------------------------------------------

    public function connectAuthorize()
    {
        $params =
        [

        ];

        return $this->request->setFormParams($params)->post(API::LIST['connectAuthorize']);
    }

    // ------------------------------------------------------------------------------

    public function connectToken()
    {

    }

    // ------------------------------------------------------------------------------

}
