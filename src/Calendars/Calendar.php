<?php namespace Nylas\Calendars;

use Nylas\Utilities\API;
use Nylas\Utilities\Helper;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validate as V;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Calendar
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/12/17
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
     * @param string $accessToken
     * @return array
     */
    public function getCalendarsList(array $params = [], string $accessToken = null)
    {
        $accessToken = $accessToken ?? $this->options->getAccessToken();

        $rule = V::keySet(
            V::keyOptional('view', V::in(['count', 'ids'])),
            V::keyOptional('limit', V::intType()->min(1)),
            V::keyOptional('offset', V::intType()->min(0))
        );

        V::doValidate($rule, $params);
        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $header = ['Authorization' => $accessToken];

        return $this->options
        ->getSync()
        ->setQuery($params)
        ->setHeaderParams($header)
        ->get(API::LIST['calendars']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get calendar
     *
     * @param string|array $calendarId
     * @param string $accessToken
     * @return array
     */
    public function getCalendar($calendarId, string $accessToken = null)
    {
        $calendarId  = Helper::fooToArray($calendarId);
        $accessToken = $accessToken ?? $this->options->getAccessToken();

        $rule = V::each(V::stringType()->notEmpty(), V::intType());

        V::doValidate($rule, $calendarId);
        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $queues = [];
        $target = API::LIST['oneCalendar'];
        $header = ['Authorization' => $accessToken];

        foreach ($calendarId as $id)
        {
            $request = $this->options
            ->getAsync()
            ->setPath($id)
            ->setHeaderParams($header);

            $queues[] = function () use ($request, $target)
            {
                return $request->get($target);
            };
        }

        $pools = $this->options->getAsync()->pool($queues, false);

        return Helper::concatPoolInfos($calendarId, $pools);
    }

    // ------------------------------------------------------------------------------

}
