<?php

declare(strict_types = 1);

namespace Nylas\Schedulers;

use Nylas\Utilities\API;
use Nylas\Utilities\Helper;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validator as V;
use GuzzleHttp\Exception\GuzzleException;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Scheduler
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2023/07/24
 */
class Scheduler
{
    // ------------------------------------------------------------------------------

    /**
     * @var Options
     */
    private Options $options;

    // ------------------------------------------------------------------------------

    /**
     * Scheduler constructor.
     *
     * @param Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
        $this->options->setSchedulerServer($this->options->getRegion());
    }

    // ------------------------------------------------------------------------------

    /**
     * Create a Scheduling Page. You can pass in an array of access_tokens to create multiple scheduling pages.
     *
     * @param array $params
     *
     * @return array
     * @throws GuzzleException
     */
    public function createASchedulingPage(array $params): array
    {
        V::doValidate(Validation::getBaseRules(), $params);

        return $this->options
            ->getSync()
            ->setFormParams($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->post(API::LIST['scheduler']);
    }

    // ------------------------------------------------------------------------------

    /**
     * Update a Scheduling Page
     *
     * @param string $pageId
     * @param array  $params
     *
     * @return array
     * @throws GuzzleException
     */
    public function updateASchedulingPage(string $pageId, array $params): array
    {
        V::doValidate(Validation::getBaseRules(), $params);

        return $this->options
            ->getSync()
            ->setPath($pageId)
            ->setFormParams($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->put(API::LIST['oneScheduler']);
    }

    // ------------------------------------------------------------------------------

    /**
     * Get available calendars
     *
     * @param string $pageId
     *
     * @return array
     * @throws GuzzleException
     */
    public function getAvailableCalendars(string $pageId): array
    {
        return $this->options
            ->getSync()
            ->setPath($pageId)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->get(API::LIST['schedulerCalendars']);
    }

    // ------------------------------------------------------------------------------

    /**
     * Returns all scheduling pages. Returns a list of all scheduling pages.
     *
     * @see https://developer.nylas.com/docs/api/v2/scheduler/#get/manage/pages
     *
     * @return array
     * @throws GuzzleException
     */
    public function returnAllSchedulingPages(): array
    {
        return $this->options
            ->getSync()
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->get(API::LIST['scheduler']);
    }

    // ------------------------------------------------------------------------------

    /**
     * Returns a scheduling page.
     *
     * @param mixed $pageId
     *
     * @return array
     */
    public function returnASchedulingPage(mixed $pageId): array
    {
        $pageId = Helper::fooToArray($pageId);

        V::doValidate(V::simpleArray(V::stringType()::notEmpty()), $pageId);

        $queues = [];

        foreach ($pageId as $id)
        {
            $request = $this->options
                ->getAsync()
                ->setPath($id)
                ->setHeaderParams($this->options->getAuthorizationHeader());

            $queues[] = static function () use ($request)
            {
                return $request->get(API::LIST['oneScheduler']);
            };
        }

        $pools = $this->options->getAsync()->pool($queues, false);

        return Helper::concatPoolInfos($pageId, $pools);
    }

    // ------------------------------------------------------------------------------

    /**
     * Delete a scheduling page.
     *
     * @param mixed $pageId
     *
     * @return array
     */
    public function deleteASchedulingPage(mixed $pageId): array
    {
        $pageId = Helper::fooToArray($pageId);

        V::doValidate(V::simpleArray(V::stringType()::notEmpty()), $pageId);

        $queues = [];

        foreach ($pageId as $id)
        {
            $request = $this->options
                ->getAsync()
                ->setPath($id)
                ->setHeaderParams($this->options->getAuthorizationHeader());

            $queues[] = static function () use ($request)
            {
                return $request->delete(API::LIST['oneScheduler']);
            };
        }

        $pools = $this->options->getAsync()->pool($queues, false);

        return Helper::concatPoolInfos($pageId, $pools);
    }

    // ------------------------------------------------------------------------------
}
