<?php

use myLibrary\php\operations\User;
use myLibrary\php\page\creator;

class PageIndex extends Creator {
    private string $v;

    public function __construct() {
        $this->v = "?v=".date("YmdHis");
        $this->page_title = "Yönetim Paneli";
        parent::__construct();
    }

    protected function customData(): array {
        return [];
    }

    protected function pageBody(): string {
        return static::setInclude(array(
            "./components/pages/panel/add.php",
            "./components/pages/panel/list.php",
            "./components/pages/panel/edit.php"
        ));
    }

    protected function customLinks(): string {
        return '
            <link rel="stylesheet" href="assets/css/plugins/dataTables.min.css">
            <link rel="stylesheet" href="assets/css/pages/panel.css'.$this->v.'">
        ';

    }

    protected function customScripts(): string {
        return '
            <script src="assets/js/plugins/dataTables.min.js"></script>
            <script src="assets/js/pages/panel.js'.$this->v.'"></script>
        ';

    }
}

$index = new PageIndex();