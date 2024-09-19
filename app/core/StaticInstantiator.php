<?php

Trait StaticInstantiator{
    public static function new(){
        return new static;
    }
}