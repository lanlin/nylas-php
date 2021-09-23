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
 * @change 2021/09/22
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
    public function returnAllEvents(array $params = []): array
    {
        V::doValidate(V::keySet(
            V::keyOptional('view', V::in(['ids', 'count'])),
            ...Validation::getBaseRules()
        ), $params);

        return $this->options
            ->getSync()
            ->setQuery($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->get(API::LIST['events']);
    }

    // ------------------------------------------------------------------------------

    /**
     * Creates an event, conference or add metadata.
     *
     * @see https://developer.nylas.com/docs/api/#post/events
     *
     * @param array $params
     *
     * @return array
     */
    public function createAnEvent(array $params, ?bool $notifyParticipants = null): array
    {
        V::doValidate(Validation::addEventRules(), $params);

        $notify = 'notify_participants';
        $query  = \is_null($notifyParticipants) ? [] : [$notify => $notifyParticipants];

        return $this->options
            ->getSync()
            ->setQuery($query)
            ->setFormParams($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
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
        V::doValidate(Validation::updateEventRules(), $params);

        $path   = $params['id'];
        $notify = 'notify_participants';
        $query  = isset($params[$notify]) ? [$notify => $params[$notify]] : [];

        unset($params['id'], $params['notify_participants']);

        return $this->options
            ->getSync()
            ->setPath($path)
            ->setQuery($query)
            ->setFormParams($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
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
        if (empty($params['account_id']))
        {
            $params['account_id'] = $this->options->getAccountId();
        }

        V::doValidate(V::keySet(
            V::key('status', V::in(['yes', 'no', 'maybe'])),
            V::key('event_id', V::stringType()->notEmpty()),
            V::key('account_id', V::stringType()->notEmpty()),
            V::keyOptional('notify_participants', V::boolType())
        ), $params);

        $notify = 'notify_participants';
        $query  = isset($params[$notify]) ? [$notify => $params[$notify]] : [];

        unset($params['notify_participants']);

        return $this->options
            ->getSync()
            ->setQuery($query)
            ->setFormParams($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
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
        $params = Helper::arrayToMulti($params);

        V::doValidate(V::simpleArray(V::keySet(
            V::key('id', V::stringType()->notEmpty()),
            ...Validation::getBaseRules(),
        )), $params);

        $queues = [];

        foreach ($params as $item)
        {
            $id = $item['id'];
            unset($item['id']);

            $request = $this->options
                ->getAsync()
                ->setPath($id)
                ->setFormParams($item)
                ->setHeaderParams($this->options->getAuthorizationHeader());

            $queues[] = static function () use ($request)
            {
                return $request->get(API::LIST['oneEvent']);
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
        $params = Helper::arrayToMulti($params);

        V::doValidate(V::simpleArray(V::keySet(
            V::key('id', V::stringType()->notEmpty()),
            V::keyOptional('notify_participants', V::boolType())
        )), $params);

        $queues = [];
        $notify = 'notify_participants';

        foreach ($params as $item)
        {
            $query = isset($item[$notify]) ? [$notify => $item[$notify]] : [];

            $request = $this->options
                ->getAsync()
                ->setPath($item['id'])
                ->setQuery($query)
                ->setHeaderParams($this->options->getAuthorizationHeader());

            $queues[] = static function () use ($request)
            {
                return $request->delete(API::LIST['oneEvent']);
            };
        }

        $evtID = Helper::generateArray($params, 'id');
        $pools = $this->options->getAsync()->pool($queues, false);

        return Helper::concatPoolInfos($evtID, $pools);
    }

    // ------------------------------------------------------------------------------
}
