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
     * Returns all labels.
     *
     * @see https://developer.nylas.com/docs/api/#get/labels
     *
     * @param array $params
     *
     * @return array
     */
    public function returnAllLabels(array $params = []): array
    {
        V::doValidate(V::keySet(
            V::keyOptional('limit', V::intType()->min(1)),
            V::keyOptional('offset', V::intType()->min(0)),
            V::keyOptional('view', V::in(['ids', 'count'])),
        ), $params);

        return $this->options
            ->getSync()
            ->setQuery($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->get(API::LIST['labels']);
    }

    // ------------------------------------------------------------------------------

    /**
     * Creates a new label.
     *
     * @see https://developer.nylas.com/docs/api/#post/labels
     *
     * @param string $displayName
     *
     * @return array
     */
    public function createALabel(?string $displayName = null): array
    {
        $params = empty($displayName) ? [] : ['display_name' => $displayName];

        return $this->options
            ->getSync()
            ->setFormParams($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->post(API::LIST['labels']);
    }

    // ------------------------------------------------------------------------------

    /**
     * Return a label by ID.
     *
     * @see https://developer.nylas.com/docs/api/#get/labels/id
     *
     * @param mixed $labelId string|string[]
     *
     * @return array
     */
    public function returnALabel(mixed $labelId): array
    {
        $labelId = Helper::fooToArray($labelId);

        V::doValidate(V::simpleArray(V::stringType()->notEmpty()), $labelId);

        $queues = [];

        foreach ($labelId as $id)
        {
            $request = $this->options
                ->getAsync()
                ->setPath($id)
                ->setHeaderParams($this->options->getAuthorizationHeader());

            $queues[] = static function () use ($request)
            {
                return $request->get(API::LIST['oneLabel']);
            };
        }

        $pools = $this->options->getAsync()->pool($queues, false);

        return Helper::concatPoolInfos($labelId, $pools);
    }

    // ------------------------------------------------------------------------------

    /**
     * Updates an existing label.
     *
     * @see https://developer.nylas.com/docs/api/#put/labels/id
     *
     * @param string $labelId
     * @param string $displayName
     *
     * @return array
     */
    public function updateALabel(string $labelId, ?string $displayName = null): array
    {
        V::doValidate(V::stringType()->notEmpty(), $labelId);

        $params = empty($displayName) ? [] : ['display_name' => $displayName];

        return $this->options
            ->getSync()
            ->setPath($labelId)
            ->setFormParams($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->put(API::LIST['oneLabel']);
    }

    // ------------------------------------------------------------------------------

    /**
     * Deletes a label. A label can be deleted even if there are messages associated with the label.
     *
     * @see https://developer.nylas.com/docs/api/#delete/labels/id
     *
     * @param mixed $labelId
     *
     * @return array
     */
    public function deleteALabel(mixed $labelId): array
    {
        $labelId = Helper::fooToArray($labelId);

        V::doValidate(V::simpleArray(V::stringType()->notEmpty()), $labelId);

        $queues = [];

        foreach ($labelId as $id)
        {
            $request = $this->options
                ->getAsync()
                ->setPath($id)
                ->setHeaderParams($this->options->getAuthorizationHeader());

            $queues[] = static function () use ($request)
            {
                return $request->delete(API::LIST['oneLabel']);
            };
        }

        $pools = $this->options->getAsync()->pool($queues, false);

        return Helper::concatPoolInfos($labelId, $pools);
    }

    // ------------------------------------------------------------------------------
}
