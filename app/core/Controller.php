<?php
abstract class Controller {
   
     public function view($view, $data = [])
     {
        view($view, $data);
     }

     public function model($modelClassName)
     {
        require_once '../app/models/' . $model . '.php';
        return new $model;
     }
}