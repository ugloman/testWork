<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\MessageDTO;

class Message
{
    public function sendSms(MessageDTO $message): void
    {
        //стопроцентная и быстрая отправка сообщения
    }
}