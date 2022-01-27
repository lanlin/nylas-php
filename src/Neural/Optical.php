<?php

namespace Nylas\Neural;

use Nylas\Utilities\API;
use Nylas\Utilities\Helper;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validator as V;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Neural
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2022/01/27
 */
class Optical
{
    // ------------------------------------------------------------------------------

    /**
     * @var \Nylas\Utilities\Options
     */
    private Options $options;

    // ------------------------------------------------------------------------------

    /**
     * Message constructor.
     *
     * @param \Nylas\Utilities\Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    // ------------------------------------------------------------------------------

    /**
     * Use Optical character recognition(OCR) to parse message file attachments. Supports PDF and TIFF.
     *
     * @see https://developer.nylas.com/docs/api/#put/neural/ocr
     *
     * @param mixed $fileId
     * @param array $pages  [1, 2, 3]
     *
     * @return array
     */
    public function opticalCharacterRecognition(string $fileId, array $pages = []): array
    {
        V::doValidate(V::arrayType()->length(0, 5), $pages);
        V::doValidate(V::simpleArray(V::intType()->min(1)), $pages);

        $pages = !empty($pages) ? [\implode(',', $pages)] : ['1,2,3,4,5'];

        return $this->options
            ->getSync()
            ->setFormParams(['pages' => $pages, 'file_id' => $fileId])
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->put(API::LIST['neuralOcr']);
    }

    // ------------------------------------------------------------------------------

    /**
     * Send feedback about optical character recognition.
     *
     * @see https://developer.nylas.com/docs/api/#post/neural/ocr/feedback
     *
     * @param mixed $fileId
     *
     * @return array
     */
    public function opticalCharacterRecognitionFeedback(mixed $fileId): array
    {
        $fileId = Helper::fooToArray($fileId);

        V::doValidate(V::simpleArray(V::stringType()->notEmpty()), $fileId);

        $queues = [];

        foreach ($fileId as $id)
        {
            $request = $this->options
                ->getAsync()
                ->setFormParams(['file_id' => $id])
                ->setHeaderParams($this->options->getAuthorizationHeader());

            $queues[] = static function () use ($request)
            {
                return $request->post(API::LIST['neuralOcrFeedback']);
            };
        }

        $pools = $this->options->getAsync()->pool($queues, false);

        return Helper::concatPoolInfos($fileId, $pools);
    }

    // ------------------------------------------------------------------------------
}
