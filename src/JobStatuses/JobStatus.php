<?php

namespace Nylas\JobStatuses;

use Exception;
use Nylas\Utilities\API;
use Nylas\Utilities\Helper;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validator as V;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Job Statuses
 * ----------------------------------------------------------------------------------
 *
 * @see https://docs.nylas.com/reference#job-statuses
 *
 * @author lanlin
 * @update jeremygriffin
 * @change 2021/09/22
 */
class JobStatus
{
    // ------------------------------------------------------------------------------

    /**
     * @var \Nylas\Utilities\Options
     */
    private Options $options;

    // ------------------------------------------------------------------------------

    /**
     * JobStatus constructor.
     *
     * @param \Nylas\Utilities\Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    // ------------------------------------------------------------------------------

    /**
     * get job-status list
     *
     * @param array $params
     *
     * @throws Exception
     *
     * @return array
     */
    public function getJobStatusesList(array $params = []): array
    {
        V::doValidate(V::keySet(
            V::keyOptional('view', V::in(['ids', 'count'])),
            ...$this->getBaseRules(),
        ), $params);

        return $this->options
            ->getSync()
            ->setQuery($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->get(API::LIST['jobStatuses']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get job-status
     *
     * @param array $params
     *
     * @return array
     */
    public function getJobStatus(array $params): array
    {
        $params = Helper::arrayToMulti($params);

        V::doValidate(V::simpleArray(V::keySet(
            V::key('job_status_id', V::stringType()->notEmpty()),
            ...$this->getBaseRules()
        )), $params);

        $queues = [];

        foreach ($params as $item)
        {
            $id = $item['job_status_id'];
            unset($item['job_status_id']);

            $request = $this->options
                ->getAsync()
                ->setPath($id)
                ->setFormParams($item)
                ->setHeaderParams($this->options->getAuthorizationHeader());

            $queues[] = static function () use ($request)
            {
                return $request->get(API::LIST['oneJobStatus']);
            };
        }

        $jobID = Helper::generateArray($params, 'job_status_id');
        $pools = $this->options->getAsync()->pool($queues, false);

        return Helper::concatPoolInfos($jobID, $pools);
    }

    // ------------------------------------------------------------------------------

    /**
     * job-status base validate rules
     *
     * @return array
     */
    private function getBaseRules(): array
    {
        return
        [
            V::keyOptional('limit', V::intType()->min(1)),
            V::keyOptional('offset', V::intType()->min(0)),
            V::keyOptional('job_status_id', V::stringType()->notEmpty()),
        ];
    }

    // ------------------------------------------------------------------------------
}
