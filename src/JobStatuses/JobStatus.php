<?php

namespace Nylas\JobStatuses;

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
 * @change 2021/05/05
 */
class JobStatus
{
    // ------------------------------------------------------------------------------

    /**
     * @var \Nylas\Utilities\Options
     */
    private $options;

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
     * @return array
     */
    public function getJobStatusesList(array $params = []): array
    {
        $rules = $this->getBaseRules();

        $rules[]     = V::keyOptional('view', V::in(['ids', 'count']));
        $accessToken = $this->options->getAccessToken();

        V::doValidate(V::keySet(...$rules), $params);
        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $header = ['Authorization' => $accessToken];

        return $this->options
            ->getSync()
            ->setQuery($params)
            ->setHeaderParams($header)
            ->get(API::LIST['jobStatus']);
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
        $rules       = $this->getBaseRules();
        $params      = Helper::arrayToMulti($params);
        $accessToken = $this->options->getAccessToken();

        $rules = V::simpleArray(V::keySet(
            V::key('job_status_id', V::stringType()->notEmpty()),
            ...$rules
        ));

        V::doValidate($rules, $params);
        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $queues = [];
        $target = API::LIST['oneJobStatus'];
        $header = ['Authorization' => $accessToken];

        foreach ($params as $item)
        {
            $id = $item['job_status_id'];
            unset($item['job_status_id']);

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
