<?php

declare(strict_types = 1);

namespace Nylas\JobStatuses;

use Nylas\Utilities\API;
use Nylas\Utilities\Helper;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validator as V;
use GuzzleHttp\Exception\GuzzleException;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Job Statuses
 * ----------------------------------------------------------------------------------
 *
 * @see https://docs.nylas.com/reference#job-statuses
 *
 * @author lanlin
 * @change 2023/07/21
 */
class JobStatus
{
    // ------------------------------------------------------------------------------

    /**
     * @var Options
     */
    private Options $options;

    // ------------------------------------------------------------------------------

    /**
     * JobStatus constructor.
     *
     * @param Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    // ------------------------------------------------------------------------------

    /**
     * Return all job statuses.
     *
     * @see https://developer.nylas.com/docs/api/v2/#get-/job-statuses
     *
     * @return array
     * @throws GuzzleException
     */
    public function returnAllJobStatuses(): array
    {
        return $this->options
            ->getSync()
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->get(API::LIST['jobStatuses']);
    }

    // ------------------------------------------------------------------------------

    /**
     * Return a job status by ID.
     *
     * @see https://developer.nylas.com/docs/api/v2/#get-/job-statuses/id
     *
     * @param mixed $jobStatusId
     *
     * @return array
     */
    public function returnAJobStatus(mixed $jobStatusId): array
    {
        $jobStatusId = Helper::fooToArray($jobStatusId);

        V::doValidate(V::simpleArray(V::stringType()::notEmpty()), $jobStatusId);

        $queues = [];

        foreach ($jobStatusId as $id)
        {
            $request = $this->options
                ->getAsync()
                ->setPath($id)
                ->setHeaderParams($this->options->getAuthorizationHeader());

            $queues[] = static function () use ($request)
            {
                return $request->get(API::LIST['oneJobStatus']);
            };
        }

        $pools = $this->options->getAsync()->pool($queues, false);

        return Helper::concatPoolInfos($jobStatusId, $pools);
    }

    // ------------------------------------------------------------------------------
}
