<?php

/**
 * @file
 * Contains \Drupal\Console\Utils\Create\Comments.
 */

namespace Drupal\Console\Utils\Create;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Datetime\DateFormatterInterface;

/**
 * Class Nodes
 * @package Drupal\Console\Utils\Create
 */
class Comments extends Base
{
    /**
     * Comments constructor.
     *
     * @param EntityTypeManagerInterface $entityTypeManager
     * @param DateFormatterInterface     $dateFormatter
     */
    public function __construct(
        EntityTypeManagerInterface $entityTypeManager,
        DateFormatterInterface $dateFormatter
    ) {
        parent::__construct($entityTypeManager, $dateFormatter);
    }

    /**
     * @param $nid
     * @param $limit
     * @param $titleWords
     * @param $timeRange
     *
     * @return array
     */
    public function createComment(
        $nid,
        $limit,
        $titleWords,
        $timeRange
    ) {
        $comments = [];

        for ($i=0; $i<$limit; $i++) {
            $comment = $this->entityTypeManager->getStorage('comment')->create(
                [
                'entity_id' => $nid,
                'entity_type' => 'node',
                'field_name' => 'comment',
                'created' => REQUEST_TIME - mt_rand(0, $timeRange),
                'uid' => $this->getUserID(),
                'status' => true,
                'subject' => $this->getRandom()->sentences(mt_rand(1, $titleWords), true),
                'language' => 'und',
                'comment_body' => ['und' => ['random body']],
                ]
            );

            $this->generateFieldSampleData($comment);

            try {
                $comment->save();
                $comments['success'][] = [
                    'nid' => $nid,
                    'cid' => $comment->id(),
                    'title' => $comment->getSubject(),
                    'created' => $this->dateFormatter->format(
                        $comment->getCreatedTime(),
                        'custom',
                        'Y-m-d h:i:s'
                    )
                ];
            } catch (\Exception $error) {
                $comments['error'][] = [
                    'title' => $comment->getTitle(),
                    'error' => $error->getMessage()
                ];
            }
        }

        return $comments;
    }
}
