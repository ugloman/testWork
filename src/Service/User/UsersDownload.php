<?php

declare(strict_types=1);

namespace App\Service\User;

use App\Service\Database;

class UsersDownload
{
    public function __construct(
        private Database $database
    ) {
    }

    /**
     * @throws \Exception
     */
    public function uploadByCsv(array $downloadFile): void
    {
        $file = fopen($downloadFile['tmp_name'], 'r');

        if ($file === false) {
            throw new \Exception('An error occurred while uploading the file');
        }

        while (($data = fgetcsv($file, 1000, ';')) !== false) {
            $number = $data[0];
            $name = $data[1];
            if (strlen($number) === 0 || strlen($name) === 0) {
                continue;
            }

            $selectQuery = sprintf("SELECT id FROM task_user WHERE name = '%s' AND number = '%s'", $name, $number);
            $users = $this->database->selectQuery($selectQuery);
            if (count($users) !== 0) {
                continue;
            }

            $query = sprintf("INSERT INTO task_user (name, number) VALUES ('%s', '%s')", $name, $number);
            $this->database->executeQuery($query);
        }

        fclose($file);
    }
}
