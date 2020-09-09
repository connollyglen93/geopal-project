<?php
/**
 * Created by PhpStorm.
 * User: glen.connolly
 * Date: 06/09/2020
 * Time: 16:35
 */

include_once 'logging.php';

class GeoJsonFileUploader
{
    /** @var string */
    private $destination;

    const STORAGE_KEY = 'geoJSONData';

    const FILE_UPLOAD_NAME = 'geoJson';

    /**
     * GeoJsonFileUploader constructor.
     */
    public function __construct()
    {
        $this->destination = uniqid('geopal_geojson_' . true) . ".json";
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public function __destruct()
    {
        if(file_exists($this->destination)){
            unlink($this->destination);
        }
        session_write_close();
    }

    /**
     * @return string
     */
    public static function getStoredData() : string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        return $_SESSION[self::STORAGE_KEY] ?? "[]";
    }

    /**
     * @param string $contents
     */
    public static function setStoredData(string $contents){
        $_SESSION[self::STORAGE_KEY] = $contents;
    }

    public function logError($error){
        writeLog($error);
    }

    /**
     * @throws UploadException
     */
    public function handleUpload(){
        $fileName = basename($_FILES[self::FILE_UPLOAD_NAME]['name']);
        $imageFileType = strtolower(pathinfo($fileName,PATHINFO_EXTENSION));

        if(!in_array($imageFileType, ['json', 'geojson'])) {
            throw new UploadException("Sorry, only JSON & GeoJSON files are allowed.");
        }

        if ($_FILES[self::FILE_UPLOAD_NAME]["size"] > 1024 * 1024) {
            echo "Sorry, your file is too large.";
            throw new UploadException("File is too large. File must be smaller than 1MB");
        }

        if(move_uploaded_file($_FILES[self::FILE_UPLOAD_NAME]["tmp_name"], $this->destination)){
            if(!file_exists($this->destination)){
                throw new UploadException("Failed to upload file");
            }
            $contents = file_get_contents($this->destination);
            $dataObj = json_decode($contents);
            if(!$dataObj){
                throw new UploadException("Failed to translate GeoJSON data: " . json_last_error_msg());
            }
            self::setStoredData($contents);
            return;
        }
        throw new UploadException("Failed to complete upload");
    }

}