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
 * @change 2021/09/22
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

        $params = ['view' => $view];

        V::doValidate(V::keySet(
            V::keyOptional('view', V::in(['ids', 'count']))
        ), $params);

        $query = empty($params['view']) ? [] : ['view' => $params['view']];

        return $this->options
            ->getSync()
            ->setQuery($query)
            ->setHeaderParams($this->options->getAuthorizationHeader())
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

        V::doValidate(V::stringType()->notEmpty(), $displayName);

        return $this->options
            ->getSync()
            ->setFormParams(['display_name' => $displayName])
            ->setHeaderParams($this->options->getAuthorizationHeader())
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

        V::doValidate(V::keySet(
            V::key('id', V::stringType()->notEmpty()),
            V::key('display_name', V::stringType()->notEmpty())
        ), $params);

        $path = $params['id'];

        unset($params['id']);

        return $this->options
            ->getSync()
            ->setPath($path)
            ->setFormParams($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
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
    public function getLabel(mixed $labelId): array
    {
        Helper::checkProviderUnit($this->options, true);

        $labelId = Helper::fooToArray($labelId);

        V::doValidate(V::simpleArray(V::stringType()->notEmpty()), $labelId);

        $queues = [];
        $target = API::LIST['oneLabel'];

        foreach ($labelId as $id)
        {
            $request = $this->options
                ->getAsync()
                ->setPath($id)
                ->setHeaderParams($this->options->getAuthorizationHeader());

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

        $params = Helper::arrayToMulti($params);

        V::doValidate(V::simpleArray(V::keySet(
            V::key('id', V::stringType()->notEmpty()),
            V::key('display_name', V::stringType()->notEmpty())
        )), $params);

        $queues = [];
        $target = API::LIST['oneLabel'];

        foreach ($params as $item)
        {
            $request = $this->options
                ->getAsync()
                ->setPath($item['id'])
                ->setFormParams(['display_name' => $item['display_name']])
                ->setHeaderParams($this->options->getAuthorizationHeader());

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
