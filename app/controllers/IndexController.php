<?php


class IndexController extends Controller{

    public function index()
    {
        $message = "Enjoy the template :D";
        
        $this->view(
            'index/index', 
            ['message' => $message]);
    }

}