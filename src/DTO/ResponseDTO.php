<?php

declare(strict_types=1);

namespace App\DTO;

class ResponseDTO
{
    public function __construct(
        public int $code,
        public string $response,
        public string $message,
    ) {
    }

}