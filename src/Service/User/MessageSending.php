<?php

declare(strict_types=1);

namespace App\Service\User;

use App\DTO\MessageDTO;
use App\Service\Database;
use App\Service\Message;

class MessageSending
{
    public function __construct(
        private Database $database,
        private Message  $message
    ) {
    }

    /**
     * @throws \Exception
     */
    public function send(string $title, string $text): void
    {
        $this->addUsersInMailQueue($title, $text);

        $usersToSendMessageQuery = sprintf(
            "SELECT q.id as queue_id, u.id as user_id, u.number as number FROM mail_queue q
                    INNER JOIN task_user u ON u.id = q.user_id
                    WHERE q.title = '%s' AND q.text = '%s' AND q.is_sent = false",
            $title,
            $text
        );
        $usersToSendMessage = $this->database->selectQuery($usersToSendMessageQuery);
        $nowDate = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        foreach ($usersToSendMessage as $user) {
            try {
                $this->message->sendSms(new MessageDTO(
                    $user['user_id'],
                    $user['number'],
                    $title,
                    $text
                ));
            } catch (\Exception $e) {
                throw new \Exception('Error sending messages, not all messages were sent');
            }
            $query = sprintf(
                "UPDATE mail_queue SET is_sent = true, date_sent = '%s' WHERE id = %d",
                $nowDate,
                $user['queue_id'],
            );
            $this->database->executeQuery($query);
        }
    }

    private function addUsersInMailQueue(string $title, string $text): void
    {
        $usersNotMailQueueQuery = sprintf(
            "SELECT task_user.id FROM task_user 
                    LEFT JOIN mail_queue q ON q.user_id = task_user.id AND (q.title = '%s' AND q.text = '%s')
                    WHERE q.id is null",
            $title,
            $text
        );

        $usersNotMailQueue = $this->database->selectQuery($usersNotMailQueueQuery);
        foreach ($usersNotMailQueue as $user) {
            $query = sprintf(
                "INSERT INTO mail_queue (user_id, title, text) VALUES (%d, '%s', '%s')",
                $user['id'],
                $title,
                $text
            );
            $this->database->executeQuery($query);
        }
    }
}
