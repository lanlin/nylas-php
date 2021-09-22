<?php

namespace Nylas\Drafts;

use Nylas\Utilities\API;
use Nylas\Utilities\Helper;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validator as V;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Drafts
 * ----------------------------------------------------------------------------------
 *
 * @info include inline image <img src="cid:file_id">
 *
 * @author lanlin
 * @change 2021/09/22
 */
class Draft
{
    // ------------------------------------------------------------------------------

    /**
     * @var \Nylas\Utilities\Options
     */
    private Options $options;

    // ------------------------------------------------------------------------------

    /**
     * Draft constructor.
     *
     * @param \Nylas\Utilities\Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    // ------------------------------------------------------------------------------

    /**
     * get drafts list
     *
     * @param mixed  $anyEmail string|string[]
     * @param string $view     ids|count
     *
     * @return array
     */
    public function getDraftsList(mixed $anyEmail = null, ?string $view = null): array
    {
        $params = [
            'view'      => $view,
            'any_email' => Helper::fooToArray($anyEmail),
        ];

        V::doValidate(V::keySet(
            V::keyOptional('view', V::in(['ids', 'count'])),
            V::keyOptional('any_email', V::simpleArray(V::email()))
        ), $params);

        return $this->options
            ->getSync()
            ->setQuery($this->getListQuery($params))
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->get(API::LIST['drafts']);
    }

    // ------------------------------------------------------------------------------

    /**
     * add draft
     *
     * @param array $params
     *
     * @return array
     */
    public function addDraft(array $params): array
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
     * update draft
     *
     * @param array $params
     *
     * @return array
     */
    public function updateDraft(array $params): array
    {
        V::doValidate(V::keySet(...$this->getUpdateRules()), $params);

        $path = $params['id'];

        unset($params['id']);

        return $this->options
            ->getSync()
            ->setPath($path)
            ->setFormParams($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->put(API::LIST['oneDraft']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get draft
     *
     * @param mixed $draftId string|string[]
     *
     * @return array
     */
    public function getDraft(mixed $draftId): array
    {
        $draftId = Helper::fooToArray($draftId);

        V::doValidate(V::simpleArray(V::stringType()->notEmpty()), $draftId);

        $queues = [];
        $target = API::LIST['oneDraft'];

        foreach ($draftId as $id)
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

        return Helper::concatPoolInfos($draftId, $pools);
    }

    // ------------------------------------------------------------------------------

    /**
     * delete draft
     *
     * @param array $params
     *
     * @return array
     */
    public function deleteDraft(array $params): array
    {
        $params = Helper::arrayToMulti($params);

        V::doValidate(V::simpleArray(V::keySet(
            V::key('id', V::stringType()->notEmpty()),
            V::key('version', V::intType()->min(0))
        )), $params);

        $queues = [];
        $target = API::LIST['oneDraft'];

        foreach ($params as $item)
        {
            $request = $this->options
                ->getAsync()
                ->setPath($item['id'])
                ->setFormParams(['version' => $item['version']])
                ->setHeaderParams($this->options->getAuthorizationHeader());

            $queues[] = static function () use ($request, $target)
            {
                return $request->delete($target);
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
     * @return \Nylas\Utilities\Validator
     */
    private function arrayOfString(): V
    {
        return V::simpleArray(V::stringType()->notEmpty());
    }

    // ------------------------------------------------------------------------------

    /**
     * array of object
     *
     * @return \Nylas\Utilities\Validator
     */
    private function arrayOfObject(): V
    {
        return V::simpleArray(
            V::keySet(
                V::key('email', V::email()),
                V::key('name', V::stringType(), false)
            )
        );
    }

    // ------------------------------------------------------------------------------

    /**
     * rules for update
     *
     * @return array
     */
    private function getUpdateRules(): array
    {
        $rules = $this->getBaseRules();

        $update =
        [
            V::key('id', V::stringType()->notEmpty()),
            V::key('version', V::intType()->min(0)),
        ];

        return \array_merge($rules, $update);
    }

    // ------------------------------------------------------------------------------

    /**
     * get list query conditions
     *
     * @param array $params
     *
     * @return array
     */
    private function getListQuery(array $params): array
    {
        $query  = [];
        $emails = \implode(',', $params['any_email']);

        if (!empty($params['view']))
        {
            $query['view'] = $params['view'];
        }

        if (!empty($emails))
        {
            $query['any_email'] = $emails;
        }

        return $query;
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
            V::keyOptional('reply_to_message_id', V::stringType()->notEmpty()),

            V::keyOptional('file_ids', $this->arrayOfString()),
            V::keyOptional('subject', V::stringType()),
            V::keyOptional('body', V::stringType()),
        ];
    }

    // ------------------------------------------------------------------------------
}
