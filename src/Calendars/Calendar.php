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
        $params =
        [
            'id'           => Helper::fooToArray($calendarId),
            'access_token' => $accessToken ?? $this->options->getAccessToken()
        ];

        $rule = V::keySet(
            V::key('id', V::each(V::stringType()->notEmpty(), V::intType())),
            V::key('access_token', V::stringType()->notEmpty())
        );

        V::doValidate($rule, $params);

        $queues = [];
        $target = API::LIST['oneCalendar'];
        $header = ['Authorization' => $params['access_token']];

        foreach ($params['id'] as $id)
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

        return $this->concatCalendarInfos($params['id'], $pools);
    }

    // ------------------------------------------------------------------------------

    /**
     * concat calendar infos
     *
     * @param array $params
     * @param array $pools
     * @return array
     */
    private function concatCalendarInfos(array $params, array $pools)
    {
        $data = [];

        foreach ($params as $index => $item)
        {
            if (isset($pools[$index]['error']))
            {
                $item = array_merge($item, $pools[$index]);
            }

            $data[$item['id']] = $item;
        }

        return $data;
    }

    // ------------------------------------------------------------------------------

}
