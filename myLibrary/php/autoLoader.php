<?php
namespace myLibrary\php;

require_once "projectConfig.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/**
 * Automatically pulls the called class
 */
class AutoLoader {
    /**
     * If there are custom locations, they are entered here
     * @var array
     */
    private $paths; 

    public function __construct() {
        $document_root = $_SERVER["DOCUMENT_ROOT"];
        $this->paths = array(
            "$document_root/public",
            $document_root
        );

        if(!empty(ProjectConfig::projectRootName)){
            $this->paths[] = ProjectConfig::projectRootPath();
        }
    }

    /**
     * Starts auto_loader
     * @return bool
     */
    public function init() : bool{
        return spl_autoload_register(function ($class) {
            foreach ($this->paths as $path) {
                $file = $path . "/{$class}.php";
                $file = str_replace("\\", "/", $file);
                if (file_exists($file)) {
                    require $file;
                    return true;
                }
            }
            return false;
        });
    }
}

$auto_loader = new AutoLoader();
$auto_loader->init();