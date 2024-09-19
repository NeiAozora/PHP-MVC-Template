<?php


class IndexController extends Controller{

    public function index()
    {
        $message = "Enjoy the template :D";
        
        view(
            'homepage/index', 
            ['message' => $message]);
    }

    public function testPost(){
        dd($_POST);
        die;
    }

}