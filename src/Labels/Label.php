<?php

namespace Nylas\Labels;

use Nylas\Utilities\API;
use Nylas\Utilities\Helper;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validator as V;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Labels
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2020/09/30
 */
class Label
{
    // ------------------------------------------------------------------------------

    /**
     * @var \Nylas\Utilities\Options
     */
    private Options $options;

    // ------------------------------------------------------------------------------

    /**
     * Label constructor.
     *
     * @param \Nylas\Utilities\Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    // ------------------------------------------------------------------------------

    /**
     * get labels list
     *
     * @param string $view ids|count
     *
     * @return array
     */
    public function getLabelsList(?string $view = null): array
    {
        Helper::checkProviderUnit($this->options, true);

        $params = [
            'view'         => $view,
            'access_token' => $this->options->getAccessToken(),
        ];

        $rule = V::keySet(
            V::key('access_token', V::stringType()->notEmpty()),
            V::keyOptional('view', V::in(['ids', 'count'])),
        );

        V::doValidate($rule, $params);

        $header = ['Authorization' => $params['access_token']];
        $query  = empty($params['view']) ? [] : ['view' => $params['view']];

        return $this->options
            ->getSync()
            ->setQuery($query)
            ->setHeaderParams($header)
            ->get(API::LIST['labels']);
    }

    // ------------------------------------------------------------------------------

    /**
     * add label
     *
     * @param string $displayName
     *
     * @return array
     */
    public function addLabel(string $displayName): array
    {
        Helper::checkProviderUnit($this->options, true);

        $accessToken = $this->options->getAccessToken();

        $rule = V::stringType()->notEmpty();

        V::doValidate($rule, $displayName);
        V::doValidate($rule, $accessToken);

        $header = ['Authorization' => $accessToken];
        $params = ['display_name'  => $displayName];

        return $this->options
            ->getSync()
            ->setFormParams($params)
            ->setHeaderParams($header)
            ->post(API::LIST['labels']);
    }

    // ------------------------------------------------------------------------------

    /**
     * update label
     *
     * @param array $params
     *
     * @return array
     */
    public function updateLabel(array $params): array
    {
        Helper::checkProviderUnit($this->options, true);

        $accessToken = $this->options->getAccessToken();

        $rule = V::keySet(
            V::key('id', V::stringType()->notEmpty()),
            V::key('display_name', V::stringType()->notEmpty())
        );

        V::doValidate($rule, $params);
        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $path   = $params['id'];
        $header = ['Authorization' => $accessToken];

        unset($params['id']);

        return $this->options
            ->getSync()
            ->setPath($path)
            ->setFormParams($params)
            ->setHeaderParams($header)
            ->put(API::LIST['oneLabel']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get label
     *
     * @param mixed $labelId string|string[]
     *
     * @return array
     */
    public function getLabel($labelId): array
    {
        Helper::checkProviderUnit($this->options, true);

        $labelId     = Helper::fooToArray($labelId);
        $accessToken = $this->options->getAccessToken();

        $rule = V::simpleArray(V::stringType()->notEmpty());

        V::doValidate($rule, $labelId);
        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $queues = [];
        $target = API::LIST['oneLabel'];
        $header = ['Authorization' => $accessToken];

        foreach ($labelId as $id)
        {
            $request = $this->options
                ->getAsync()
                ->setPath($id)
                ->setHeaderParams($header);

            $queues[] = static function () use ($request, $target)
            {
                return $request->get($target);
            };
        }

        $pools = $this->options->getAsync()->pool($queues, false);

        return Helper::concatPoolInfos($labelId, $pools);
    }

    // ------------------------------------------------------------------------------

    /**
     * delete label
     *
     * @param array $params
     *
     * @return array
     */
    public function deleteLabel(array $params): array
    {
        Helper::checkProviderUnit($this->options, true);

        $params      = Helper::arrayToMulti($params);
        $accessToken = $this->options->getAccessToken();

        $rule = V::simpleArray(V::keySet(
            V::key('id', V::stringType()->notEmpty()),
            V::key('display_name', V::stringType()->notEmpty())
        ));

        V::doValidate($rule, $params);
        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $queues = [];
        $target = API::LIST['oneLabel'];
        $header = ['Authorization' => $accessToken];

        foreach ($params as $item)
        {
            $request = $this->options
                ->getAsync()
                ->setPath($item['id'])
                ->setFormParams(['display_name' => $item['display_name']])
                ->setHeaderParams($header);

            $queues[] = static function () use ($request, $target)
            {
                return $request->delete($target);
            };
        }

        $labId = Helper::generateArray($params, 'id');
        $pools = $this->options->getAsync()->pool($queues, false);

        return Helper::concatPoolInfos($labId, $pools);
    }

    // ------------------------------------------------------------------------------
}
