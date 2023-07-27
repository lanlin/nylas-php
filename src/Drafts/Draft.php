<?php

declare(strict_types = 1);

namespace Nylas\Drafts;

use function count;
use function implode;
use function is_array;
use function array_keys;
use function array_values;

use Nylas\Utilities\API;
use Nylas\Utilities\Helper;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validator as V;
use GuzzleHttp\Exception\GuzzleException;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Drafts
 * ----------------------------------------------------------------------------------
 *
 * @info include inline image <img src="cid:file_id">
 *
 * @author lanlin
 * @change 2023/07/21
 */
class Draft
{
    // ------------------------------------------------------------------------------

    /**
     * @var Options
     */
    private Options $options;

    // ------------------------------------------------------------------------------

    /**
     * Draft constructor.
     *
     * @param Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    // ------------------------------------------------------------------------------

    /**
     * Returns all drafts.
     *
     * @see https://developer.nylas.com/docs/api/v2/#get-/drafts
     *
     * @param array $params
     *
     * @return array
     * @throws GuzzleException
     */
    public function returnAllDrafts(array $params = []): array
    {
        V::doValidate($this->getQueryRules(), $params);

        if (!empty($params['any_email']))
        {
            $params['any_email'] = implode(',', $params['any_email']);
        }

        return $this->options
            ->getSync()
            ->setQuery($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->get(API::LIST['drafts']);
    }

    // ------------------------------------------------------------------------------

    /**
     * Creates a new draft.
     *
     * @see https://developer.nylas.com/docs/api/v2/#post-/drafts
     *
     * @param array $params
     *
     * @return array
     * @throws GuzzleException
     */
    public function createADraft(array $params): array
    {
        V::doValidate(V::keySet(...$this->getBaseRules()), $params);

        return $this->options
            ->getSync()
            ->setFormParams($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->post(API::LIST['drafts']);
    }

    // ------------------------------------------------------------------------------

    /**
     * Returns a draft by ID.
     *
     * @see https://developer.nylas.com/docs/api/v2/#get-/drafts/id
     *
     * @param mixed $draftId string|string[]
     *
     * @return array
     */
    public function returnADraft(mixed $draftId): array
    {
        $draftId = Helper::fooToArray($draftId);

        V::doValidate(V::simpleArray(V::stringType()::notEmpty()), $draftId);

        $queues = [];

        foreach ($draftId as $id)
        {
            $request = $this->options
                ->getAsync()
                ->setPath($id)
                ->setHeaderParams($this->options->getAuthorizationHeader());

            $queues[] = static function () use ($request)
            {
                return $request->get(API::LIST['oneDraft']);
            };
        }

        $pools = $this->options->getAsync()->pool($queues, false);

        return Helper::concatPoolInfos($draftId, $pools);
    }

    // ------------------------------------------------------------------------------

    /**
     * Updates an existing draft by ID.
     *
     * @see https://developer.nylas.com/docs/api/v2/#put-/drafts/id
     *
     * @param string $draftId
     * @param array  $params
     *
     * @return array
     * @throws GuzzleException
     */
    public function updateADraft(string $draftId, array $params): array
    {
        V::doValidate(V::stringType()::notEmpty(), $draftId);

        V::doValidate(V::keySet(
            V::key('version', V::intType()::min(0)),
            ...$this->getBaseRules()
        ), $params);

        return $this->options
            ->getSync()
            ->setPath($draftId)
            ->setFormParams($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->put(API::LIST['oneDraft']);
    }

    // ------------------------------------------------------------------------------

    /**
     * Deletes a draft by ID. The draft version must be specified otherwise it will return an error.
     *
     * @see https://developer.nylas.com/docs/api/v2/#delete-/drafts/id
     *
     * @param array $params
     *
     * @return array
     */
    public function deleteADraft(array $params): array
    {
        $params = Helper::arrayToMulti($params);

        V::doValidate(V::simpleArray(V::keySet(
            V::key('id', V::stringType()::notEmpty()),
            V::key('version', V::intType()::min(0))
        )), $params);

        $queues = [];

        foreach ($params as $item)
        {
            $request = $this->options
                ->getAsync()
                ->setPath($item['id'])
                ->setFormParams(['version' => $item['version']])
                ->setHeaderParams($this->options->getAuthorizationHeader());

            $queues[] = static function () use ($request)
            {
                return $request->delete(API::LIST['oneDraft']);
            };
        }

        $dftId = Helper::generateArray($params, 'id');
        $pools = $this->options->getAsync()->pool($queues, false);

        return Helper::concatPoolInfos($dftId, $pools);
    }

    // ------------------------------------------------------------------------------

    /**
     * array of string
     *
     * @return V
     */
    private function arrayOfString(): V
    {
        return V::simpleArray(V::stringType()::notEmpty());
    }

    // ------------------------------------------------------------------------------

    /**
     * array of object
     *
     * @return V
     */
    private function arrayOfObject(): V
    {
        return V::simpleArray(
            V::keySet(
                V::key('email', V::email()),
                V::key('name', V::stringType()::notEmpty())
            )
        );
    }

    // ------------------------------------------------------------------------------

    /**
     * draft base validate rules
     *
     * @return array
     */
    private function getBaseRules(): array
    {
        return
        [
            V::keyOptional('to', $this->arrayOfObject()),
            V::keyOptional('cc', $this->arrayOfObject()),
            V::keyOptional('bcc', $this->arrayOfObject()),
            V::keyOptional('from', $this->arrayOfObject()),
            V::keyOptional('reply_to', $this->arrayOfObject()),
            V::keyOptional('reply_to_message_id', V::stringType()::notEmpty()),

            V::keyOptional('body', V::stringType()),
            V::keyOptional('subject', V::stringType()),
            V::keyOptional('file_ids', $this->arrayOfString()),
            V::keyOptional('metadata', $this->metadataRules()),
        ];
    }

    // ------------------------------------------------------------------------------

    /**
     * get metadata array rules
     *
     * @see https://developer.nylas.com/docs/api/metadata/#keep-in-mind
     *
     * @return V
     */
    private function metadataRules(): V
    {
        return V::callback(static function (mixed $input): bool
        {
            if (!is_array($input) || count($input) > 50)
            {
                return false;
            }

            $keys = array_keys($input);
            $isOk = V::each(V::stringType()::length(1, 40))->validate($keys);

            if (!$isOk)
            {
                return false;
            }

            // https://developer.nylas.com/docs/api/metadata/#delete-metadata
            return V::each(V::stringType()::length(0, 500))->validate(array_values($input));
        });
    }

    // ------------------------------------------------------------------------------

    /**
     * get messages list filter rules
     *
     * @return V
     */
    private function getQueryRules(): V
    {
        return V::keySet(
            V::keyOptional('in', V::stringType()::notEmpty()),
            V::keyOptional('to', V::email()),
            V::keyOptional('cc', V::email()),
            V::keyOptional('bcc', V::email()),
            V::keyOptional('limit', V::intType()::min(1)),
            V::keyOptional('offset', V::intType()::min(0)),
            V::keyOptional('view', V::in(['ids', 'count', 'expanded'])),
            V::keyOptional('unread', V::boolType()),
            V::keyOptional('starred', V::boolType()),
            V::keyOptional('subject', V::stringType()::notEmpty()),
            V::keyOptional('filename', V::stringType()::notEmpty()),
            V::keyOptional('thread_id', V::stringType()::notEmpty()),
            V::keyOptional('any_email', V::simpleArray(V::email())),
            V::keyOptional('has_attachment', V::equals(true)),
            V::keyOptional('last_message_after', V::timestampType()),
            V::keyOptional('last_message_before', V::timestampType()),
            V::keyOptional('started_message_after', V::timestampType()),
            V::keyOptional('started_message_before', V::timestampType()),

            // @see https://developer.nylas.com/docs/api/metadata/#keep-in-mind
            V::keyOptional('metadata_key', V::stringType()::length(1, 40)),
            V::keyOptional('metadata_value', V::stringType()::length(1, 500)),
            V::keyOptional('metadata_paire', V::stringType()::length(3, 27100)),
            V::keyOptional('metadata_search', V::stringType()::notEmpty()),
        );
    }

    // ------------------------------------------------------------------------------
}
