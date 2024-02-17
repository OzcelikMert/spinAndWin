<?php
namespace myLibrary\php\page;

abstract class Elements extends Helper {
    /**
     * The name of the page that appear on the browser tabs.
     * Default name <b>Blank Page</b>.
     * @var string
     */
    private string $page_name = "";
    protected string $page_title = "";

    protected function __construct(){
        $this->page_name = str_replace(".php", "", basename($_SERVER['REQUEST_URI']));
        $this->page_title = (empty($this->page_title)) ? ucfirst($this->page_name) : $this->page_title;
    }

    /**
     * Default structure of page. <b>(Defined head and scripts tools)</b>
     * @return void
     */
    protected function pageSkeleton() : void{
        require "./components/tools/skeleton.php";
    }

    /**
     * Created for use variables of in page
     * * @return void
     */
    protected function definer() : void{
        define("custom_data", $this->customData());
        define("page_name", $this->page_name);
        define("page_title", $this->page_title);
        define("custom_links", $this->customLinks());
        define("custom_scripts", $this->customScripts());
        define("page_body", $this->pageBody());
    }

    /**
     * This is body elements included in web page
     * @return string
     */
    protected function pageBody() : string { return ""; }

    /**
     * Call and create this function if u want to call custom styles files
     * @return string
     */
    protected function customLinks() : string { return ""; }

    /**
     * Call and create this function if u want to call custom scripts files
     * @return string
     */
    protected function customScripts() : string { return ""; }

    /**
     * You can write extra codes in this
     * @return array
     */
    protected function customData() : array { return []; }
}