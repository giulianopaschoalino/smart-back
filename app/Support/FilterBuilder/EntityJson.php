<?php

declare(strict_types=1);

namespace App\Support\FilterBuilder;


abstract class EntityJson implements \JsonSerializable
{

    public function jsonToObject(\stdClass $jsonData)
    {
        $vars = get_object_vars($jsonData);
        foreach ($vars as $key => $value) {
            $this->$key = $value;
        }
    }


    public function jsonSerialize(): \stdClass
    {
        $vars = get_object_vars($this);
        $obj = new \stdClass();
        foreach ($vars as $key => $value) {
            $obj->$key = $value;
        }
        return $obj;
    }

    public function jsonSetObject(\stdClass $jsonData)
    {
        $vars = get_object_vars($jsonData);
        foreach ($vars as $key => $value) {
            $method = "set" . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            } else {
                if (property_exists(get_class($this), $key)) {
                    $this->$key = $value;
                }
            }
        }
    }

    public function toArray(): array
    {
        $vars = get_object_vars($this);
        $result = array();
        foreach ($vars as $key => $value) {
            $method = "get" . ucfirst($key);
            $key = strtoupper($key);
            $valor = $value;
            if (method_exists($this, $method)) {
                $valor = $this->$method();
            }
            $result[$key] = $valor;
        }
        return $result;
    }


    public function arrayObjectCast(array $arrayOfObject, $className)
    {
        $arr = [];
        if (!empty($arrayOfObject)) {
            $obj = $arrayOfObject[0];
            if (is_array($obj) && count(array_filter(array_keys($obj), 'is_string')) > 0) {
                $obj = (object) $obj;
            }
            if (get_class($obj) == "stdClass") {
                foreach ($arrayOfObject as $item) {
                    $e = new $className();
                    if (method_exists($e, 'jsonSetObject')) {
                        $e->jsonSetObject($item);
                        $arr[] = $e;
                    }
                }
            } elseif (get_class($obj) == $className) {
                return $arrayOfObject;
            }
        }

        return $arr;
    }

    public function __get(string $name)
    {
        return $this->{$name};
    }

    public function __set(string $name, $value): void
    {
        $this->{$name} = $value;
    }
}