<?php

declare(strict_types=1);

namespace App\DTO;

class MessageDTO
{
    public function __construct(
        public int    $userId,
        public string $number,
        public string $title,
        public string $text
    ) {
    }
}