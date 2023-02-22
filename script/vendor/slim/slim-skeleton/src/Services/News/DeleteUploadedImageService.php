<?php

declare(strict_types=1);

namespace App\Services\News;

class DeleteUploadedImageService
{
    /**
     * @var string
     */
    private $path;

    public function __construct()
    {
        $this->path = PUBLIC_PATH . UPLOAD_PATH . '/';
    }

    /**
     * @return bool
     */
    public function deleteImage($fileName): bool
    {
        $filePath = $this->path . $fileName;

        if (file_exists($filePath)) {
            return unlink($filePath);
        }

        return false;
    }
}
