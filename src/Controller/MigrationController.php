<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\ResponseDTO;
use App\Service\Database;
use App\Service\ResponseInterface;

class MigrationController implements ResponseInterface
{
    public function __construct(
        private Database $database
    ) {
    }

    public function migration(): ResponseDTO
    {
        try {
            $this->database->migrate();
        } catch (\Throwable $e) {
            return new ResponseDTO(
                (int)$e->getCode(),
                'error',
                $e->getMessage()
            );
        }

        return new ResponseDTO(
            ResponseInterface::HTTP_OK,
            'success',
            'Migrations is migrate'
        );
    }
}