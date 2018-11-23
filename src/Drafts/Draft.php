<?php namespace Nylas\Drafts;

use Nylas\Utilities\API;
use Nylas\Utilities\Request;
use Nylas\Utilities\Validate as V;
use Nylas\Exceptions\NylasException;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Drafts
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/11/23
 */
class Draft
{

    // ------------------------------------------------------------------------------

    /**
     * @var Request
     */
    private $request;

    // ------------------------------------------------------------------------------

    /**
     * Hosted constructor.
     */
    public function __construct()
    {
        $this->request = new Request();
    }

    // ------------------------------------------------------------------------------

    /**
     * get drafts list
     *
     * @param array $params
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function getDraftsList(array $params)
    {
        $rule = V::keySet(
            V::key('access_token', V::stringType()::notEmpty()),
            V::key('any_email', V::arrayVal()->each(V::email(), V::intType()), false)
        );

        if (!$rule->validate($params))
        {
            throw new NylasException('invalid params');
        }

        $emails = implode(',', $params['any_email'] ?? []);
        $header = ['Authorization' => $params['access_token']];
        $query  = empty($emails) ? [] : ['any_email' => $emails];

        return $this->request
        ->setQuery($query)
        ->setHeaderParams($header)
        ->get(API::LIST['drafts']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get draft
     *
     * @param array $params
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function getDraft(array $params)
    {
        $rule = V::keySet(
            V::key('id', V::stringType()::notEmpty()),
            V::key('access_token', V::stringType()::notEmpty())
        );

        if (!$rule->validate($params))
        {
            throw new NylasException('invalid params');
        }

        $path   = [$params['id']];
        $header = ['Authorization' => $params['access_token']];

        return $this->request
        ->setPath($path)
        ->setHeaderParams($header)
        ->get(API::LIST['oneDraft']);
    }

    // ------------------------------------------------------------------------------

    /**
     * add draft
     *
     * @param array $params
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function addDraft(array $params)
    {
        $rules = $this->getBaseRules();

        if (!V::keySet(...$rules)->validate($params))
        {
            throw new NylasException('invalid params');
        }

        $header = ['Authorization' => $params['access_token']];

        unset($params['access_token']);

        return $this->request
        ->setFormParams($params)
        ->setHeaderParams($header)
        ->post(API::LIST['drafts']);
    }

    // ------------------------------------------------------------------------------

    /**
     * update draft
     *
     * @param array $params
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function updateDraft(array $params)
    {
        $rules = $this->getUpdateRules();

        if (!V::keySet(...$rules)->validate($params))
        {
            throw new NylasException('invalid params');
        }

        $path   = [$params['id']];
        $header = ['Authorization' => $params['access_token']];

        unset($params['id'], $params['access_token']);

        return $this->request
        ->setPath($path)
        ->setFormParams($params)
        ->setHeaderParams($header)
        ->put(API::LIST['oneDraft']);
    }

    // ------------------------------------------------------------------------------

    /**
     * delete draft
     *
     * @param array $params
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function deleteDraft(array $params)
    {
        $rule = V::keySet(
            V::key('id', V::stringType()::notEmpty()),
            V::key('version', V::stringType()::notEmpty()),
            V::key('access_token', V::stringType()::notEmpty())
        );

        if (!$rule->validate($params))
        {
            throw new NylasException('invalid params');
        }

        $path   = [$params['id']];
        $header = ['Authorization' => $params['access_token']];

        unset($params['id'], $params['access_token']);

        return $this->request
        ->setPath($path)
        ->setFormParams($params)
        ->setHeaderParams($header)
        ->delete(API::LIST['oneDraft']);
    }

    // ------------------------------------------------------------------------------

    /**
     * array of string
     *
     * @return \Respect\Validation\Validator
     */
    private function arrayOfString()
    {
        return V::arrayVal()->each(
            V::stringType()::notEmpty(),
            V::intType()
        );
    }

    // ------------------------------------------------------------------------------

    /**
     * array of object
     *
     * @return \Respect\Validation\Validator
     */
    private function arrayOfObject()
    {
        return V::each(V::keySet(
            V::key('name', V::stringType()),
            V::key('email', V::email())
        ));
    }

    // ------------------------------------------------------------------------------

    /**
     * rules for update
     *
     * @return array
     */
    private function getUpdateRules()
    {
        $rules = $this->getBaseRules();

        $update =
        [
            V::key('id', V::stringType()::notEmpty()),
            V::key('version', V::stringType()::notEmpty()),
        ];

        return array_merge($rules, $update);
    }

    // ------------------------------------------------------------------------------

    /**
     * draft base validate rules
     *
     * @return array
     */
    private function getBaseRules()
    {
        return
        [
            V::key('to', $this->arrayOfObject(), false),
            V::key('cc', $this->arrayOfObject(), false),
            V::key('bcc', $this->arrayOfObject(), false),
            V::key('from', $this->arrayOfObject(), false),
            V::key('reply_to', $this->arrayOfObject(), false),

            V::key('file_ids', $this->arrayOfString(), false),
            V::key('subject', V::stringType(), false),
            V::key('body', V::stringType(), false),

            V::key('access_token', V::stringType()::notEmpty())
        ];
    }

    // ------------------------------------------------------------------------------

}
