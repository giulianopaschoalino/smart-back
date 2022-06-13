<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class FilterQueryBuilder
{
    protected $request;


    public function __construct($request)
    {

        $this->initializeRequest($request);

    }

//    public function __construct($class)
//    {
//        $this->class = $class;
//
//        return $this;
//    }

    public static function for($request)
    {

        return (new static($request));
    }


    protected function initializeRequest(?Request $request = null): static
    {

        $this->request = $request
            ? QueryBuilderRequest::fromRequest($request)
            : app(QueryBuilderRequest::class);

        return $this;
    }


    public function __get($name)
    {
        return $this->class->{$name};
    }

    public function __set($name, $value)
    {
        $this->class->{$name} = $value;
    }
}