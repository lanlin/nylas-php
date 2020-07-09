<?php

namespace Nylas\Events;

use Nylas\Utilities\API;
use Nylas\Utilities\Helper;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validator as V;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Events
 * ----------------------------------------------------------------------------------
 *
 * @see https://docs.nylas.com/reference#event-limitations
 *
 * @author lanlin
 * @change 2020/04/26
 */
class Event
{
    // ------------------------------------------------------------------------------

    /**
     * @var \Nylas\Utilities\Options
     */
    private Options $options;

    // ------------------------------------------------------------------------------

    /**
     * Event constructor.
     *
     * @param \Nylas\Utilities\Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    // ------------------------------------------------------------------------------

    /**
     * get events list
     *
     * @param array $params
     *
     * @return array
     */
    public function getEventsList(array $params = []): array
    {
        $rules = $this->getBaseRules();

        $accessToken = $this->options->getAccessToken();

        V::doValidate(V::keySet(...$rules), $params);
        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $header = ['Authorization' => $accessToken];

        return $this->options
            ->getSync()
            ->setQuery($params)
            ->setHeaderParams($header)
            ->get(API::LIST['events']);
    }

    // ------------------------------------------------------------------------------

    /**
     * add event
     *
     * @param array $params
     *
     * @return array
     */
    public function addEvent(array $params): array
    {
        $accessToken = $this->options->getAccessToken();

        V::doValidate($this->addEventRules(), $params);
        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $notify = 'notify_participants';
        $header = ['Authorization' => $accessToken];
        $query  = isset($params[$notify]) ? [$notify => $params[$notify]] : [];

        unset($params['notify_participants']);

        return $this->options
            ->getSync()
            ->setQuery($query)
            ->setFormParams($params)
            ->setHeaderParams($header)
            ->post(API::LIST['events']);
    }

    // ------------------------------------------------------------------------------

    /**
     * update event
     *
     * @param array $params
     *
     * @return array
     */
    public function updateEvent(array $params): array
    {
        $accessToken = $this->options->getAccessToken();

        V::doValidate($this->updateEventRules(), $params);
        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $path   = $params['id'];
        $notify = 'notify_participants';
        $header = ['Authorization' => $accessToken];
        $query  = isset($params[$notify]) ? [$notify => $params[$notify]] : [];

        unset($params['id'], $params['notify_participants']);

        return $this->options
            ->getSync()
            ->setPath($path)
            ->setQuery($query)
            ->setFormParams($params)
            ->setHeaderParams($header)
            ->put(API::LIST['oneEvent']);
    }

    // ------------------------------------------------------------------------------

    /**
     * rsvping
     *
     * @param array $params
     *
     * @return mixed
     */
    public function rsvping(array $params)
    {
        $params['account_id'] = $params['account_id'] ?? $this->options->getAccountId();

        $accessToken = $this->options->getAccessToken();

        $rules = V::keySet(
            V::key('status', V::in(['yes', 'no', 'maybe'])),
            V::key('event_id', V::stringType()->notEmpty()),
            V::key('account_id', V::stringType()->notEmpty()),
            V::keyOptional('notify_participants', V::boolType())
        );

        V::doValidate($rules, $params);
        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $notify = 'notify_participants';
        $header = ['Authorization' => $accessToken];
        $query  = isset($params[$notify]) ? [$notify => $params[$notify]] : [];

        unset($params['notify_participants']);

        return $this->options
            ->getSync()
            ->setQuery($query)
            ->setFormParams($params)
            ->setHeaderParams($header)
            ->post(API::LIST['oneEvent']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get event
     *
     * @param array $params
     *
     * @return array
     */
    public function getEvent(array $params): array
    {
        $rules       = $this->getBaseRules();
        $params      = Helper::arrayToMulti($params);
        $accessToken = $this->options->getAccessToken();

        $rules = V::simpleArray(V::keySet(
            V::key('id', V::stringType()->notEmpty()),
            ...$rules
        ));

        V::doValidate($rules, $params);
        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $queues = [];
        $target = API::LIST['oneEvent'];
        $header = ['Authorization' => $accessToken];

        foreach ($params as $item)
        {
            $id = $item['id'];
            unset($item['id']);

            $request = $this->options
                ->getAsync()
                ->setPath($id)
                ->setFormParams($item)
                ->setHeaderParams($header);

            $queues[] = static function () use ($request, $target)
            {
                return $request->get($target);
            };
        }

        $evtID = Helper::generateArray($params, 'id');
        $pools = $this->options->getAsync()->pool($queues, false);

        return Helper::concatPoolInfos($evtID, $pools);
    }

    // ------------------------------------------------------------------------------

    /**
     * delete event
     *
     * @param array $params
     *
     * @return array
     */
    public function deleteEvent(array $params): array
    {
        $params      = Helper::arrayToMulti($params);
        $accessToken = $this->options->getAccessToken();

        $rule = V::simpleArray(V::keySet(
            V::key('id', V::stringType()->notEmpty()),
            V::keyOptional('notify_participants', V::boolType())
        ));

        V::doValidate($rule, $params);
        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $queues = [];
        $target = API::LIST['oneEvent'];
        $notify = 'notify_participants';
        $header = ['Authorization' => $accessToken];

        foreach ($params as $item)
        {
            $query = isset($item[$notify]) ? [$notify => $item[$notify]] : [];

            $request = $this->options
                ->getAsync()
                ->setPath($item['id'])
                ->setQuery($query)
                ->setHeaderParams($header);

            $queues[] = static function () use ($request, $target)
            {
                return $request->delete($target);
            };
        }

        $evtID = Helper::generateArray($params, 'id');
        $pools = $this->options->getAsync()->pool($queues, false);

        return Helper::concatPoolInfos($evtID, $pools);
    }

    // ------------------------------------------------------------------------------

    /**
     * event base validate rules
     *
     * @return array
     */
    private function getBaseRules(): array
    {
        return
        [
            V::keyOptional('limit', V::intType()->min(1)),
            V::keyOptional('offset', V::intType()->min(0)),
            V::keyOptional('event_id', V::stringType()->notEmpty()),
            V::keyOptional('calendar_id', V::stringType()->notEmpty()),

            V::keyOptional('title', V::stringType()->notEmpty()),
            V::keyOptional('location', V::stringType()->notEmpty()),
            V::keyOptional('description', V::stringType()->notEmpty()),
            V::keyOptional('show_cancelled', V::boolType()),
            V::keyOptional('expand_recurring', V::boolType()),

            V::keyOptional('ends_after', V::timestampType()),
            V::keyOptional('ends_before', V::timestampType()),
            V::keyOptional('starts_after', V::timestampType()),
            V::keyOptional('starts_before', V::timestampType()),
        ];
    }

    // ------------------------------------------------------------------------------

    /**
     * rules for update event
     *
     * @return \Nylas\Utilities\Validator
     */
    private function updateEventRules(): V
    {
        return V::keySet(
            V::key('id', V::stringType()->notEmpty()),
            V::keyOptional('when', $this->timeRules()),
            V::keyOptional('busy', V::boolType()),
            V::keyOptional('title', V::stringType()->notEmpty()),
            V::keyOptional('location', V::stringType()->notEmpty()),
            V::keyOptional('description', V::stringType()->notEmpty()),
            V::keyOptional('notify_participants', V::boolType()),
            V::keyOptional('participants', V::simpleArray(V::keySet(
                V::key('email', V::email()),
                V::key('status', V::stringType()),
                V::key('name', V::stringType()),
                V::key('comment', V::stringType())
            )))
        );
    }

    // ------------------------------------------------------------------------------

    /**
     * rules for add event
     *
     * @return \Nylas\Utilities\Validator
     */
    private function addEventRules(): V
    {
        return V::keySet(
            V::key('when', $this->timeRules()),
            V::key('calendar_id', V::stringType()->notEmpty()),
            V::keyOptional('busy', V::boolType()),
            V::keyOptional('title', V::stringType()->notEmpty()),
            V::keyOptional('location', V::stringType()->notEmpty()),
            V::keyOptional('recurrence', V::arrayType()),
            V::keyOptional('description', V::stringType()->notEmpty()),
            V::keyOptional('notify_participants', V::boolType()),
            V::keyOptional('participants', V::simpleArray(V::keySet(
                V::key('email', V::email()),
                V::key('status', V::stringType()),
                V::key('name', V::stringType()),
                V::key('comment', V::stringType())
            )))
        );
    }

    // ------------------------------------------------------------------------------

    /**
     * get event time rules
     *
     * @return \Nylas\Utilities\Validator
     */
    private function timeRules(): V
    {
        return V::anyOf(

            // time
            V::keySet(V::key('time', V::timestampType())),

            // date
            V::keySet(V::key('date', V::date('Y-m-d'))),

            // timespan
            V::keySet(
                V::key('end_time', V::timestampType()),
                V::key('start_time', V::timestampType())
            ),

            // date span
            V::keySet(
                V::key('end_date', V::date('Y-m-d')),
                V::key('start_date', V::date('Y-m-d'))
            )
        );
    }

    // ------------------------------------------------------------------------------
}
