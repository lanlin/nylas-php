<?php

declare(strict_types = 1);

namespace Nylas\Labels;

use Nylas\Utilities\API;
use Nylas\Utilities\Helper;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validator as V;
use GuzzleHttp\Exception\GuzzleException;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Labels
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2023/07/21
 */
class Label
{
    // ------------------------------------------------------------------------------

    /**
     * @var Options
     */
    private Options $options;

    // ------------------------------------------------------------------------------

    /**
     * Label constructor.
     *
     * @param Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    // ------------------------------------------------------------------------------

    /**
     * Returns all labels.
     *
     * @see https://developer.nylas.com/docs/api/v2/#get-/labels
     *
     * @param array $params
     *
     * @return array
     * @throws GuzzleException
     */
    public function returnAllLabels(array $params = []): array
    {
        V::doValidate(V::keySet(
            V::keyOptional('limit', V::intType()::min(1)),
            V::keyOptional('offset', V::intType()::min(0)),
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
     * @see https://developer.nylas.com/docs/api/v2/#post-/labels
     *
     * @param null|string $displayName
     *
     * @return array
     * @throws GuzzleException
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
     * @see https://developer.nylas.com/docs/api/v2/#get-/labels/id
     *
     * @param mixed $labelId string|string[]
     *
     * @return array
     */
    public function returnALabel(mixed $labelId): array
    {
        $labelId = Helper::fooToArray($labelId);

        V::doValidate(V::simpleArray(V::stringType()::notEmpty()), $labelId);

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
     * @see https://developer.nylas.com/docs/api/v2/#put-/labels/id
     *
     * @param string      $labelId
     * @param null|string $displayName
     *
     * @return array
     * @throws GuzzleException
     */
    public function updateALabel(string $labelId, ?string $displayName = null): array
    {
        V::doValidate(V::stringType()::notEmpty(), $labelId);

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
     * @see https://developer.nylas.com/docs/api/v2/#delete-/labels/id
     *
     * @param mixed $labelId
     *
     * @return array
     */
    public function deleteALabel(mixed $labelId): array
    {
        $labelId = Helper::fooToArray($labelId);

        V::doValidate(V::simpleArray(V::stringType()::notEmpty()), $labelId);

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
