<?php


class App {
    public function __construct() {
        $this->initRoutes();
    }

    public function setExceptionHandler(callable $handlerClosure){
        Router::setExceptionHandler($handlerClosure);
    }

    public function run(){
        Router::getInstance()->dispatch(); // Dispatch using Router
    }

    private function initRoutes() {
        require_once dirname(dirname(dirname(__FILE__))) . '/routes/web.php'; // Ensure to include the Router class
    }


}
