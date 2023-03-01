<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\ResponseDTO;
use App\Service\FileTypesInterface;
use App\Service\ResponseInterface;
use App\Service\User\MessageSending;
use App\Service\User\UsersDownload;
use Exception;

class UserController implements ResponseInterface, FileTypesInterface
{
    public function __construct(
        private UsersDownload  $usersDownload,
        private MessageSending $messageSending
    ) {
    }

    public function usersUpload(): ResponseDTO
    {
        $downloadFile = $_FILES['file'];
        if ($downloadFile === null || $downloadFile['type'] !== FileTypesInterface::CSV_TYPE) {
            return new ResponseDTO(
                ResponseInterface::HTTP_BAD_REQUEST,
                'error',
                'The file name must be "file". The file type must be CSV.'
            );
        }

        try {
            $this->usersDownload->uploadByCsv($downloadFile);
        } catch (Exception $e) {
            return new ResponseDTO(
                (int)$e->getCode(),
                'error',
                $e->getMessage(),
            );
        }

        return new ResponseDTO(
            ResponseInterface::HTTP_OK,
            'success',
            'File uploaded successfully',
        );
    }

    public function usersSend(): ResponseDTO
    {
        $title = $_POST['title'] ?: '';
        $text = $_POST['text'] ?: '';

        if (strlen($title) === 0) {
            return new ResponseDTO(
                ResponseInterface::HTTP_BAD_REQUEST,
                'error',
                'Title is not found',
            );
        }

        if (strlen($text) === 0) {
            return new ResponseDTO(
                ResponseInterface::HTTP_BAD_REQUEST,
                'error',
                'Text is not found',
            );
        }

        try {
            $this->messageSending->send($title, $text);
        } catch (Exception $e) {
            return new ResponseDTO(
                (int)$e->getCode(),
                'error',
                $e->getMessage(),
            );
        }

        return new ResponseDTO(
            ResponseInterface::HTTP_OK,
            'success',
            'Message sent to users successfully',
        );
    }

}