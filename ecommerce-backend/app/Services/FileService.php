<?php
namespace App\Services;

class FileService {
    private string $uploadDir = __DIR__ . '/../../public/uploads/';

    public function __construct() {
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }

    /**
     * Upload an image and return its public URL
     */
    public function uploadImage($file) {
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            return null;
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = uniqid('img_', true) . '.' . $extension;
        $targetFile = $this->uploadDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            // Return public path (relative to your web root)
            // Adjust base URL if project is in a subfolder
            return '/uploads/' . $fileName;
        }

        return null;
    }

    /**
     * Upload multiple images
     */
    public function uploadMultiple($filesArray) {
        $urls = [];
        
        // This handles standard multiple file inputs (array or multiple fields)
        if (isset($filesArray['name']) && is_array($filesArray['name'])) {
            foreach ($filesArray['name'] as $i => $name) {
                if ($filesArray['error'][$i] === UPLOAD_ERR_OK) {
                    $file = [
                        'name' => $filesArray['name'][$i],
                        'tmp_name' => $filesArray['tmp_name'][$i]
                    ];
                    $url = $this->uploadImage($file);
                    if ($url) $urls[] = $url;
                }
            }
        } else {
            // Simple logic for non-array multiple field uploads if needed
            foreach ($filesArray as $file) {
                $url = $this->uploadImage($file);
                if ($url) $urls[] = $url;
            }
        }

        return $urls;
    }
}
