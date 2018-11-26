<?php namespace Nylas\Drafts;

use Nylas\Utilities\API;
use Nylas\Utilities\Options;
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
     * @var \Nylas\Utilities\Options
     */
    private $options;

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
     * @param string $anyEmail
     * @param string $accessToken
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function getDraftsList(string $anyEmail = null, string $accessToken = null)
    {
        $params = ['access_token' => $accessToken ?? $this->options->getAccessToken()];

        if ($anyEmail) { $params['any_email'] = $anyEmail; }

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

        return $this->options->getRequest()
        ->setQuery($query)
        ->setHeaderParams($header)
        ->get(API::LIST['drafts']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get draft
     *
     * @param string $draftId
     * @param string $accessToken
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function getDraft(string $draftId, string $accessToken = null)
    {
        $params =
        [
            'id'           => $draftId,
            'access_token' => $accessToken ?? $this->options->getAccessToken()
        ];

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

        return $this->options->getRequest()
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

        $params['access_token'] =
        $params['access_token'] ?? $this->options->getAccessToken();

        if (!V::keySet(...$rules)->validate($params))
        {
            throw new NylasException('invalid params');
        }

        $header = ['Authorization' => $params['access_token']];

        unset($params['access_token']);

        return $this->options->getRequest()
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

        $params['access_token'] =
        $params['access_token'] ?? $this->options->getAccessToken();

        if (!V::keySet(...$rules)->validate($params))
        {
            throw new NylasException('invalid params');
        }

        $path   = [$params['id']];
        $header = ['Authorization' => $params['access_token']];

        unset($params['id'], $params['access_token']);

        return $this->options->getRequest()
        ->setPath($path)
        ->setFormParams($params)
        ->setHeaderParams($header)
        ->put(API::LIST['oneDraft']);
    }

    // ------------------------------------------------------------------------------

    /**
     * delete draft
     *
     * @param string $draftId
     * @param string $version
     * @param string $accessToken
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function deleteDraft(string $draftId, string $version, string $accessToken = null)
    {
        $params =
        [
            'id'           => $draftId,
            'version'      => $version,
            'access_token' => $accessToken ?? $this->options->getAccessToken(),
        ];

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

        return $this->options->getRequest()
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
