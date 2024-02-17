<?php
namespace myLibrary\php\operations;

class Server {
    public string $SERVER_NAME;
    public string $SERVER_IP;
    public string $SENDER_SERVER_NAME;
    public string $SENDER_SERVER_IP;

    public function __construct() {
        $this->SERVER_NAME = $_SERVER["SERVER_NAME"];
        $this->SERVER_IP = $_SERVER["SERVER_ADDR"];
        $parse_url = parse_url($_SERVER["HTTP_REFERER"]);
        $this->SENDER_SERVER_NAME = isset($parse_url["host"]) ? $parse_url["host"] : $parse_url["path"];
        $this->SENDER_SERVER_IP = gethostbyname($this->SENDER_SERVER_NAME);
    }

    public function fromThis() : bool{
        $value = true;

        if(!$this->SERVER_IP == $this->SENDER_SERVER_IP) {
            $value = false;
        }

        return $value;
    }

    /**
     * Creates path for folder or file.
     * @param string $path
     * @return bool
     */
    public static function createPath(string $path): bool {
        if (is_dir($path)) return true;
        $prev_path = substr($path, 0, strrpos($path, '/', -2) + 1 );
        $return = static::createPath($prev_path);
        return ($return && is_writable($prev_path)) ? mkdir($path) : false;
    }

    /**
     * Writes the specified value into the file. <b>(fopen mode 'wb')</b>
     * @param string $file_location
     * @param string $text
     * @return bool
     */
    public static function writeToFile(string $file_location, string $text) : bool{
        $value = false;

        if(is_writable($file_location)){
            // Open File
            if ($file = fopen($file_location, "wb")){
                if(fwrite($file, $text)){
                    fclose($file);
                    $value = true;
                }
            }

        }

        return $value;
    }

    /**
     * Uploads as the official <b>.webp</b> to the location you specified
     * @param string $file
     * @param string $path
     * @param string $name
     * @param int $quality
     * @param int $type
     * @return bool
     */
    public static function uploadImage(
        string $file,
        string $path,
        string $name,
        int $quality = 100,
        int $type = IMAGETYPE_WEBP
    ) : bool{
        $info = getimagesize($file);
        $image_create = null;

        $image_create = match ($info[2]) {
            IMAGETYPE_GIF => imagecreatefromgif($file),
            IMAGETYPE_JPEG => imagecreatefromjpeg($file),
            IMAGETYPE_PNG => imagecreatefrompng($file),
            IMAGETYPE_BMP => imagecreatefrombmp($file),
            IMAGETYPE_WEBP => imagecreatefromwebp($file),
            default => null
        };

        $value = $image_create != null;

        if($value){
            try{
                self::createPath($path);
                $target_path = "{$path}/{$name}";
                switch ($type){
                    case IMAGETYPE_GIF: imagegif($image_create, $target_path); break;
                    case IMAGETYPE_JPEG: imagejpeg($image_create, $target_path, $quality); break;
                    case IMAGETYPE_PNG: imagepng($image_create, $target_path, $quality); break;
                    case IMAGETYPE_BMP: imagebmp($image_create, $target_path); break;
                    case IMAGETYPE_WEBP: imagewebp($image_create, $target_path, $quality); break;
                }
                imagedestroy($image_create);
            }catch (\Exception $e){
                $value = false;
            }
        }

        return $value;
    }

    /**
     * Get url folders name  ex: "url.com/folder1/folder2/folder3/" => return [folder1, folder2, folder3]
     * @return array
     */
    public static function getURLFolders() : array {
        $data = explode("/", $_SERVER["PHP_SELF"]);
        if(is_array($data) && count($data) > 0){
            // Remove ""
            array_splice($data, 0, 1);
            if(count($data) > 1){
                // Remove file name
                array_splice($data, (count($data) - 1), 1);
            }
        }
        return $data;
    }
}