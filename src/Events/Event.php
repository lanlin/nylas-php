<?php namespace Nylas\Events;

use Nylas\Utilities\API;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validate as V;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Events
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/11/23
 */
class Event
{

    // ------------------------------------------------------------------------------

    /**
     * @var \Nylas\Utilities\Options
     */
    private $options;

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
     * @return array
     */
    public function getEventsList(array $params)
    {
        $rules = $this->getBaseRules();

        $params['access_token'] =
        $params['access_token'] ?? $this->options->getAccessToken();

        V::doValidate(V::keySet(...$rules), $params);

        $header = ['Authorization' => $params['access_token']];

        unset($params['access_token']);

        return $this->options
        ->getRequest()
        ->setQuery($params)
        ->setHeaderParams($header)
        ->get(API::LIST['events']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get event
     *
     * @param array $params
     * @return array
     */
    public function getEvent(array $params)
    {
        $temps = [V::key('id', V::stringType()->notEmpty())];
        $rules = array_merge($temps, $this->getBaseRules());

        $params['access_token'] =
        $params['access_token'] ?? $this->options->getAccessToken();

        V::doValidate(V::keySet(...$rules), $params);

        $path   = $params['id'];
        $header = ['Authorization' => $params['access_token']];

        unset($params['id'], $params['access_token']);

        return $this->options
        ->getRequest()
        ->setPath($path)
        ->setFormParams($params)
        ->setHeaderParams($header)
        ->get(API::LIST['oneEvent']);
    }

    // ------------------------------------------------------------------------------

    /**
     * add event
     *
     * @param array $params
     * @return array
     */
    public function addEvent(array $params)
    {
        $params['access_token'] =
        $params['access_token'] ?? $this->options->getAccessToken();

        V::doValidate($this->addEventRules(), $params);

        $header = ['Authorization' => $params['access_token']];
        $query  = ['notify_participants' => $params['notify_participants']];

        unset($params['access_token'], $params['notify_participants']);

        return $this->options
        ->getRequest()
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
     * @return array
     */
    public function updateEvent(array $params)
    {
        $params['access_token'] =
        $params['access_token'] ?? $this->options->getAccessToken();

        V::doValidate($this->updateEventRules(), $params);

        $path   = $params['id'];
        $header = ['Authorization' => $params['access_token']];
        $query  = ['notify_participants' => $params['notify_participants']];

        unset($params['id'], $params['access_token'], $params['notify_participants']);

        return $this->options
        ->getRequest()
        ->setPath($path)
        ->setQuery($query)
        ->setFormParams($params)
        ->setHeaderParams($header)
        ->put(API::LIST['oneEvent']);
    }

    // ------------------------------------------------------------------------------

    /**
     * delete event
     *
     * @param array $params
     * @return mixed
     */
    public function deleteEvent(array $params)
    {
        $params['access_token'] =
        $params['access_token'] ?? $this->options->getAccessToken();

        $rule = V::keySet(
            V::key('id', V::stringType()->notEmpty()),
            V::key('access_token', V::stringType()->notEmpty()),
            V::keyOptional('notify_participants', V::boolType())
        );

        V::doValidate($rule, $params);

        $header = ['Authorization' => $params['access_token']];

        $temps = empty($params['notify_participants']);
        $query = $temps ? [] : ['notify_participants' => $params['notify_participants']];

        return $this->options
        ->getRequest()
        ->setPath($params['id'])
        ->setQuery($query)
        ->setHeaderParams($header)
        ->delete(API::LIST['oneEvent']);
    }

    // ------------------------------------------------------------------------------

    /**
     * @param array $params
     * @return mixed
     */
    public function rsvping(array $params)
    {
        $params['account_id'] =
        $params['account_id'] ?? $this->options->getAccountId();

        $params['access_token'] =
        $params['access_token'] ?? $this->options->getAccessToken();

        $rules = V::keySet(
            V::key('status', V::in(['yes', 'no', 'maybe'])),
            V::key('event_id', V::stringType()->notEmpty()),
            V::key('account_id', V::stringType()->notEmpty()),
            V::key('access_token', V::stringType()->notEmpty()),
            V::keyOptional('notify_participants', V::boolType())
        );

        V::doValidate($rules, $params);

        $temps = empty($params['notify_participants']);
        $query = $temps ? [] : ['notify_participants' => $params['notify_participants']];

        $header = ['Authorization' => $params['access_token']];
        unset($params['access_token'], $params['notify_participants']);

        return $this->options
        ->getRequest()
        ->setQuery($query)
        ->setFormParams($params)
        ->setHeaderParams($header)
        ->post(API::LIST['oneEvent']);
    }

    // ------------------------------------------------------------------------------

    /**
     * event base validate rules
     *
     * @return array
     */
    private function getBaseRules()
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
            V::keyOptional('start_after', V::timestampType()),
            V::keyOptional('start_before', V::timestampType()),

            V::key('access_token', V::stringType()->notEmpty())
        ];
    }

    // ------------------------------------------------------------------------------

    /**
     * rules for update event
     *
     * @return \Respect\Validation\Validator
     */
    private function updateEventRules()
    {
        return V::keySet(
            V::key('id', V::stringType()->notEmpty()),
            V::key('access_token', V::stringType()->notEmpty()),

            V::keyOptional('when', $this->timeRules()),
            V::keyOptional('busy', V::boolType()),
            V::keyOptional('title', V::stringType()->notEmpty()),
            V::keyOptional('location', V::stringType()->notEmpty()),
            V::keyOptional('description', V::stringType()->notEmpty()),
            V::keyOptional('notify_participants', V::boolType()),

            V::keyOptional('participants', V::arrayVal()->each(V::keySet(
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
     * @return \Respect\Validation\Validator
     */
    private function addEventRules()
    {
        return V::keySet(
            V::key('when', $this->timeRules()),
            V::key('calendar_id', V::stringType()->notEmpty()),
            V::key('access_token', V::stringType()->notEmpty()),

            V::keyOptional('busy', V::boolType()),
            V::keyOptional('title', V::stringType()->notEmpty()),
            V::keyOptional('location', V::stringType()->notEmpty()),
            V::keyOptional('recurrence', V::arrayType()),
            V::keyOptional('description', V::stringType()->notEmpty()),
            V::keyOptional('notify_participants', V::boolType()),

            V::keyOptional('participants', V::arrayVal()->each(V::keySet(
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
     * @return \Respect\Validation\Validator
     */
    private function timeRules()
    {
        return V::oneOf(

            // time
            V::keySet(V::key('time', V::timestampType())),

            // date
            V::keySet(V::key('date', V::date('c'))),

            // timespan
            V::keySet(
                V::key('end_time', V::timestampType()),
                V::key('start_time', V::timestampType())
            ),

            // date span
            V::keySet(
                V::key('end_date', V::date('c')),
                V::key('start_date', V::date('c'))
            )
        );
    }

    // ------------------------------------------------------------------------------

}
