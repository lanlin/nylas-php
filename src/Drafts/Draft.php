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
 * @change 2020/04/26
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
     * @param mixed $anyEmail string|string[]
     *
     * @return array
     */
    public function getDraftsList($anyEmail): array
    {
        $params =['access_token' => $this->options->getAccessToken()];

        if (!empty($anyEmail))
        {
            $params['any_email'] = Helper::fooToArray($anyEmail);
        }

        $rule = V::keySet(
            V::key('access_token', V::stringType()->notEmpty()),
            V::keyOptional('any_email', V::simpleArray(V::email()))
        );

        V::doValidate($rule, $params);

        $emails = \implode(',', ($params['any_email'] ?? []));
        $header = ['Authorization' => $params['access_token']];
        $query  = empty($emails) ? [] : ['any_email' => $emails];

        return $this->options
            ->getSync()
            ->setQuery($query)
            ->setHeaderParams($header)
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
        $rules = $this->getBaseRules();

        $accessToken = $this->options->getAccessToken();

        V::doValidate(V::keySet(...$rules), $params);
        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $header = ['Authorization' => $accessToken];

        return $this->options
            ->getSync()
            ->setFormParams($params)
            ->setHeaderParams($header)
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
        $rules = $this->getUpdateRules();

        $accessToken = $this->options->getAccessToken();

        V::doValidate(V::keySet(...$rules), $params);

        $path   = $params['id'];
        $header = ['Authorization' => $accessToken];

        unset($params['id']);

        return $this->options
            ->getSync()
            ->setPath($path)
            ->setFormParams($params)
            ->setHeaderParams($header)
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
    public function getDraft($draftId): array
    {
        $draftId     = Helper::fooToArray($draftId);
        $accessToken = $this->options->getAccessToken();

        $rule = V::simpleArray(V::stringType()->notEmpty());

        V::doValidate($rule, $draftId);
        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $queues = [];
        $target = API::LIST['oneDraft'];
        $header = ['Authorization' => $accessToken];

        foreach ($draftId as $id)
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
        $params      = Helper::arrayToMulti($params);
        $accessToken = $this->options->getAccessToken();

        $rule = V::simpleArray(V::keySet(
            V::key('id', V::stringType()->notEmpty()),
            V::key('version', V::intType()->min(0))
        ));

        V::doValidate($rule, $params);
        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $queues = [];
        $target = API::LIST['oneDraft'];
        $header = ['Authorization' => $accessToken];

        foreach ($params as $item)
        {
            $request = $this->options
                ->getAsync()
                ->setPath($item['id'])
                ->setFormParams(['version' => $item['version']])
                ->setHeaderParams($header);

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
                V::key('name', V::stringType(), false),
                V::key('email', V::email())
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

            V::keyOptional('file_ids', $this->arrayOfString()),
            V::keyOptional('subject', V::stringType()),
            V::keyOptional('body', V::stringType()),
        ];
    }

    // ------------------------------------------------------------------------------
}
