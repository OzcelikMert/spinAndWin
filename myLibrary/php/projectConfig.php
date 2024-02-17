<?php
namespace myLibrary\php;

class ProjectConfig {
    const projectRootName = "spinAndWin";

    public static function projectRootPath() { return $_SERVER["DOCUMENT_ROOT"] . "/" . ProjectConfig::projectRootName; }

}