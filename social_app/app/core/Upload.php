<?php
/**
 * Upload - validates and stores image uploads.
 * Returns ['status' => 'none'|'ok'|'error', 'filename' => ..., 'error' => ...]
 */
class Upload
{
    private const TYPES   = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
    private const FOLDERS = ['avatars', 'posts'];

    public static function image(?array $file, string $folder): array
    {
        if (!in_array($folder, self::FOLDERS, true)) {
            throw new InvalidArgumentException('Invalid upload folder.');
        }
        if ($file === null || !isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return ['status' => 'none'];
        }
        if (is_array($file['error'])) {
            return self::fail('Invalid upload.');
        }

        $max = (int) config('max_upload_bytes', 2097152);
        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return self::fail('The image is too large (maximum ' . round($max / 1048576, 1) . ' MB).');
            default:
                return self::fail('The image could not be uploaded. Please try again.');
        }

        if ($file['size'] > $max) {
            return self::fail('The image is too large (maximum ' . round($max / 1048576, 1) . ' MB).');
        }
        if (!is_uploaded_file($file['tmp_name'])) {
            return self::fail('Invalid upload.');
        }

        $info = @getimagesize($file['tmp_name']);      // fails for non-images
        if ($info === false) {
            return self::fail('Only real image files are allowed (JPG, PNG, GIF or WEBP).');
        }
        $mime = $info['mime'] ?? '';
        if (class_exists('finfo')) {
            $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        }
        if (!isset(self::TYPES[$mime])) {
            return self::fail('Only JPG, PNG, GIF or WEBP images are allowed.');
        }

        $dir = PUBLIC_PATH . '/uploads/' . $folder;
        if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
            return self::fail('The upload folder is not writable.');
        }

        $name = bin2hex(random_bytes(16)) . '.' . self::TYPES[$mime];   // never trust the client file name
        if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $name)) {
            return self::fail('The image could not be saved. Check folder permissions.');
        }
        return ['status' => 'ok', 'filename' => $name];
    }

    public static function delete(?string $filename, string $folder): void
    {
        if ($filename === null || $filename === '' || !in_array($folder, self::FOLDERS, true)) {
            return;
        }
        $path = PUBLIC_PATH . '/uploads/' . $folder . '/' . basename($filename);
        if (is_file($path)) {
            @unlink($path);
        }
    }

    private static function fail(string $message): array
    {
        return ['status' => 'error', 'error' => $message];
    }
}
