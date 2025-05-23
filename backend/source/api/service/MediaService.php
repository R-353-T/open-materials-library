<?php

namespace oml\api\service;

use oml\php\abstract\Service;
use oml\php\error\InternalError;
use Throwable;
use WP_Error;

class MediaService extends Service
{
    /**
     * @param array $file A file to upload
     * @return string The relative path to the uploaded file
     */
    public function upload(array &$file): string
    {
        require_once ABSPATH . "wp-admin/includes/file.php";

        $metadata = wp_handle_upload($file, ["test_form" => false]);

        if (isset($metadata["error"])) {
            wp_die($metadata["error"]);
        }

        return str_replace(site_url("/"), "", $metadata["url"]);
    }


    /**
     * @param string $relative The relative path to the file to delete
     * @return bool|WP_Error Whether the file was deleted successfully
     */
    public function delete(string $relative): bool|WP_Error
    {
        try {
            return unlink(ABSPATH . $relative);
        } catch (Throwable $error) {
            return new InternalError($error->getMessage(), $error->getTraceAsString());
        }
    }
}
