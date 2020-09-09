<?php
/**
 * Created by PhpStorm.
 * User: glen.connolly
 * Date: 07/09/2020
 * Time: 16:35
 */

namespace classes;
include_once '../functions/logging.php';
include '../classes/UploadException.php';

/**
 * Class GeoJsonFileUploader
 * @package classes
 */
class GeoJsonFileUploader
{
    /** @var string */
    private $destination;

    /**
     *
     */
    const STORAGE_KEY = 'geoJSONData';

    /**
     *
     */
    const FILE_UPLOAD_NAME = 'geoJson';

    /**
     * GeoJsonFileUploader constructor.
     * Sets up a session for storing the uploaded GeoJSON data on construction
     */
    public function __construct()
    {
        $this->destination = uniqid('tmp/geopal_geojson_' . true) . ".json";
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    /**
     * Removes the temporarily stored uploaded file if it was uploaded successfully
     */
    public function __destruct()
    {
        if (file_exists($this->destination)) {
            unlink($this->destination);
        }
        session_write_close();
    }

    /**
     * Retrieve the data stored in the session
     * @return string
     */
    public static function getStoredData(): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        return $_SESSION[self::STORAGE_KEY] ?? "[]";
    }

    /**
     * Store the data in the session
     * @param string $contents
     */
    public static function setStoredData(string $contents)
    {
        $_SESSION[self::STORAGE_KEY] = $contents;
    }

    /**
     * Log an error in the tmp folder
     * @param $error
     */
    public function logError($error)
    {
        writeLog($error);
    }

    /**
     * Validate the uploaded file.
     * This includes checking the extension of the file, the mime type of the file and the size of the file
     * Throw an UploadException if the file is considered invalid
     * @throws UploadException
     */
    private function validateUpload()
    {
        $fileName = basename($_FILES[self::FILE_UPLOAD_NAME]['name']);
        $imageFileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $mimetype = mime_content_type($_FILES[self::FILE_UPLOAD_NAME]['tmp_name']);

        if (!in_array($mimetype, ['application/json', 'text/plain'])) {
            throw new UploadException("Sorry, only files of a JSON or GeoJSON type are allowed.");
        }

        if (!in_array($imageFileType, ['json', 'geojson'])) {
            throw new UploadException("Sorry, only JSON and GeoJSON files are allowed.");
        }

        if ($_FILES[self::FILE_UPLOAD_NAME]["size"] > 1024 * 1024) {
            throw new UploadException("File is too large. File must be smaller than 1MB");
        }
    }

    /**
     * Handle the upload of the file.
     * This includes validating the file, safely uploading the file, retrieving the contents of the file and storing them in the session
     * @throws UploadException
     */
    public function handleUpload()
    {
        $this->validateUpload();

        if (move_uploaded_file($_FILES[self::FILE_UPLOAD_NAME]["tmp_name"], $this->destination)) {
            if (!file_exists($this->destination)) {
                throw new UploadException("Failed to upload file");
            }
            $contents = file_get_contents($this->destination);
            $dataObj = json_decode($contents);
            if (!$dataObj) {
                throw new UploadException("Failed to translate GeoJSON data: " . json_last_error_msg());
            }
            self::setStoredData($contents);
            return;
        }
        throw new UploadException("Failed to complete upload");
    }

}