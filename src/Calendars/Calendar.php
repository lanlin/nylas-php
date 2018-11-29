<?php namespace Nylas\Calendars;

use Nylas\Utilities\API;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validate as V;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Calendar
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/11/23
 */
class Calendar
{

    // ------------------------------------------------------------------------------

    /**
     * @var \Nylas\Utilities\Options
     */
    private $options;

    // ------------------------------------------------------------------------------

    /**
     * Calendar constructor.
     *
     * @param \Nylas\Utilities\Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    // ------------------------------------------------------------------------------

    /**
     * get calendars list
     *
     * @param array $params
     * @return array
     */
    public function getCalendarsList(array $params)
    {
        $params['access_token'] =
        $params['access_token'] ?? $this->options->getAccessToken();

        $rule = V::keySet(
            V::key('access_token', V::stringType()->notEmpty()),

            V::keyOptional('view', V::in(['count', 'ids'])),
            V::keyOptional('limit', V::intType()->min(1)),
            V::keyOptional('offset', V::intType()->min(0))
        );

        V::doValidate($rule, $params);

        $header = ['Authorization' => $params['access_token']];

        unset($params['access_token']);

        return $this->options
        ->getRequest()
        ->setQuery($params)
        ->setHeaderParams($header)
        ->get(API::LIST['calendars']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get calendar
     *
     * @param string $calendarId
     * @param string $accessToken
     * @return array
     */
    public function getCalendar(string $calendarId, string $accessToken = null)
    {
        $params =
        [
            'id'           => $calendarId,
            'access_token' => $accessToken ?? $this->options->getAccessToken()
        ];

        $rule = V::keySet(
            V::key('id', V::stringType()->notEmpty()),
            V::key('access_token', V::stringType()->notEmpty())
        );

        V::doValidate($rule, $params);

        $header = ['Authorization' => $params['access_token']];

        return $this->options
        ->getRequest()
        ->setPath($params['id'])
        ->setHeaderParams($header)
        ->get(API::LIST['oneCalendar']);
    }

    // ------------------------------------------------------------------------------

}
