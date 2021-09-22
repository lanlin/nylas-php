<?php

namespace Nylas\Threads;

use Nylas\Labels\Label;
use Nylas\Utilities\API;
use Nylas\Folders\Folder;
use Nylas\Utilities\Helper;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validator as V;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Threads Smart Update
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2021/07/21
 */
class Smart
{
    // ------------------------------------------------------------------------------

    /**
     * @var \Nylas\Utilities\Options
     */
    private Options $options;

    // ------------------------------------------------------------------------------

    /**
     * Search constructor.
     *
     * @param \Nylas\Utilities\Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    // ------------------------------------------------------------------------------

    /**
     * add labels to thread
     *
     * @param string $threadId
     * @param mixed  $labels   string|string[]
     *
     * @return array
     */
    public function addLabels(string $threadId, mixed $labels): array
    {
        return $this->updateLabels($threadId, $labels);
    }

    // ------------------------------------------------------------------------------

    /**
     * remove labels from thread
     *
     * @param string $threadId
     * @param mixed  $labels   string|string[]
     *
     * @return array
     */
    public function removeLabels(string $threadId, mixed $labels): array
    {
        return $this->updateLabels($threadId, null, $labels);
    }

    // ------------------------------------------------------------------------------

    /**
     * archive thread
     *
     * @param string $threadId
     *
     * @return array
     */
    public function archive(string $threadId): array
    {
        return Helper::isLabel($this->options) ?
        $this->updateLabels($threadId, null, ['inbox']) :
        $this->updateFolder($threadId, 'archive');
    }

    // ------------------------------------------------------------------------------

    /**
     * unarchive thread
     *
     * @param string $threadId
     *
     * @return array
     */
    public function unarchive(string $threadId): array
    {
        return Helper::isLabel($this->options) ?
        $this->updateLabels($threadId, ['inbox'], ['archive']) :
        $this->updateFolder($threadId, 'inbox');
    }

    // ------------------------------------------------------------------------------

    /**
     * move thread to trash
     *
     * @param string $threadId
     *
     * @return array
     */
    public function trash(string $threadId): array
    {
        return Helper::isLabel($this->options) ?
        $this->updateLabels($threadId, ['trash'], ['inbox']) :
        $this->updateFolder($threadId, 'trash');
    }

    // ------------------------------------------------------------------------------

    /**
     * move from "label|folder" to other "label|folder" by name
     *
     * @param string $threadId
     * @param string $from
     * @param string $goto
     *
     * @return array
     */
    public function move(string $threadId, string $from, string $goto): array
    {
        return Helper::isLabel($this->options) ?
        $this->updateLabels($threadId, [$goto], [$from]) :
        $this->updateFolder($threadId, $goto);
    }

    // ------------------------------------------------------------------------------

    /**
     * set thread to start
     *
     * @param mixed $threadId string|string[]
     *
     * @return array
     */
    public function star(mixed $threadId): array
    {
        $params = ['starred' => true];

        return $this->updateOneField($threadId, $params);
    }

    // ------------------------------------------------------------------------------

    /**
     * set thread to un-star
     *
     * @param mixed $threadId string|string[]
     *
     * @return array
     */
    public function unstar(mixed $threadId): array
    {
        $params = ['starred' => false];

        return $this->updateOneField($threadId, $params);
    }

    // ------------------------------------------------------------------------------

    /**
     * mark thread as read
     *
     * @param mixed $threadId string|string[]
     *
     * @return array
     */
    public function markAsRead(mixed $threadId): array
    {
        $params = ['unread' => false];

        return $this->updateOneField($threadId, $params);
    }

    // ------------------------------------------------------------------------------

    /**
     * mark thread as unread
     *
     * @param mixed $threadId string|string[]
     *
     * @return array
     */
    public function markAsUnread(mixed $threadId): array
    {
        $params = ['unread' => true];

        return $this->updateOneField($threadId, $params);
    }

    // ------------------------------------------------------------------------------

    /**
     * move thread to folder by id
     *
     * @param mixed  $threadId string|string[]
     * @param string $folderId
     *
     * @return array
     */
    public function moveToFolder(mixed $threadId, string $folderId): array
    {
        Helper::checkProviderUnit($this->options, false);

        V::doValidate(V::stringType()->notEmpty(), $folderId);

        $params = ['folder_id' => $folderId];

        return $this->updateOneField($threadId, $params);
    }

    // ------------------------------------------------------------------------------

    /**
     * move thread to labels by id
     *
     * @param mixed $threadId string|string[]
     * @param array $labelIds
     *
     * @return array
     */
    public function moveToLabel(mixed $threadId, array $labelIds): array
    {
        Helper::checkProviderUnit($this->options, true);

        V::doValidate(V::simpleArray(V::stringType()->notEmpty()), $labelIds);

        $params = ['label_ids' => $labelIds];

        return $this->updateOneField($threadId, $params);
    }

    // ------------------------------------------------------------------------------

    /**
     * update thread folder
     *
     * @param string $threadId
     * @param string $folder
     *
     * @return array
     */
    private function updateFolder(string $threadId, string $folder): array
    {
        $folderId   = null;
        $allFolders = (new Folder($this->options))->returnAllFolders();

        foreach ($allFolders as $row)
        {
            if ($row['name'] === $folder)
            {
                $folderId = $row['id'];
                break;
            }
        }

        return $this->moveToLabel($threadId, $folderId);
    }

    // ------------------------------------------------------------------------------

    /**
     * update thread labels (update with name or display name)
     *
     * @param string $threadId
     * @param mixed  $add      string|string[]
     * @param mixed  $del      string|string[]
     *
     * @return array
     */
    private function updateLabels(string $threadId, mixed $add = [], mixed $del = []): array
    {
        $tmpLabels = [];
        $allLabels = (new Label($this->options))->getLabelsList();
        $threadMsg = (new Thread($this->options))->returnsAThread($threadId);
        $nowLabels = $threadMsg[$threadId]['labels'] ?? [];

        $add = Helper::fooToArray($add);
        $del = Helper::fooToArray($del);

        // check all labels
        foreach ($allLabels as $label)
        {
            $secA = !empty($label['name']) && \in_array($label['name'], $add, true);
            $secB = empty($label['name'])  && \in_array($label['display_name'], $add, true);

            if ($secA || $secB)
            {
                $tmpLabels[] = $label['id'];
            }
        }

        // check current thread labels
        foreach ($nowLabels as $label)
        {
            $secA = !empty($label['name']) && \in_array($label['name'], $del, true);
            $secB = empty($label['name'])  && \in_array($label['display_name'], $del, true);

            if ($secA || $secB)
            {
                continue;
            }

            $tmpLabels[] = $label['id'];
        }

        return $this->moveToLabel($threadId, $tmpLabels);
    }

    // ------------------------------------------------------------------------------

    /**
     * update the specific field of thread
     *
     * @param mixed $threadId string|string[]
     * @param array $params
     *
     * @return array
     */
    private function updateOneField(mixed $threadId, array $params): array
    {
        $threadId    = Helper::fooToArray($threadId);
        $accessToken = $this->options->getAuthorizationHeader();

        $rule = V::simpleArray(V::stringType()->notEmpty());

        V::doValidate($rule, $threadId);
        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $queues = [];
        $target = API::LIST['oneThread'];
        $header = ['Authorization' => $accessToken];

        foreach ($threadId as $id)
        {
            $request = $this->options
                ->getAsync()
                ->setPath($id)
                ->setFormParams($params)
                ->setHeaderParams($header);

            $queues[] = static function () use ($request, $target)
            {
                return $request->put($target);
            };
        }

        $pools = $this->options->getAsync()->pool($queues, false);

        return Helper::concatPoolInfos($threadId, $pools);
    }

    // ------------------------------------------------------------------------------
}
