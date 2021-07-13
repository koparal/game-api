<?php

class Controller
{
    /**
     * @param $name
     * @param array $data
     */
    public function view($name, $data = [])
    {
        extract($data);
        
        require './resources/views/' . strtolower($name) . '.php';
    }

    /**
     * @param $name
     * @return mixed
     */
    public function model($name)
    {
        require './App/Models/' . strtolower($name) . '.php';

        return new $name();
    }
}