<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Helper\Model;

/**
 * Description of EntityJson
 *
 * @author renan
 */
abstract class EntityJson implements \JsonSerializable
{
    public function jsonToObject(\stdClass $jsonData)
    {
        $vars = get_object_vars($jsonData);
        foreach ($vars as $key => $value) {
            $this->$key = $value;
        }
    }

    public function jsonSerialize()
    {
        $vars = get_object_vars($this);
        $obj = new \stdClass();
        foreach ($vars as $key => $value) {
            $obj->$key = $value;
        }
        return $obj;
    }

    public function jsonSerializeUpperKey()
    {
        $vars = get_object_vars($this);
        $obj = new \stdClass();
        foreach ($vars as $key => $value) {
            $key = strtoupper($key);
            $obj->$key = $value;
        }
        return $obj;
    }

    public function jsonSerializeLowerKey()
    {
        $vars = get_object_vars($this);
        $obj = new \stdClass();
        foreach ($vars as $key => $value) {
            $key = strtolower($key);
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

    public function fill(\stdClass $jsonData)
    {
        $vars = get_object_vars($jsonData);
        foreach ($vars as $key => $value) {
            $campo = strtolower($key);
            $method = "set" . ucfirst($campo);
            if (method_exists($this, $method)) {
                $this->$method($value);
            } else {
                if (property_exists(get_class($this), $campo)) {
                    $this->$campo = $value;
                }
            }
        }
    }

    public function toArray()
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

    /**
     * Converte um array de objeto stdClass para uma entidade
     * @param $className
     * @param array $arrayOfObject
     * @return array
     */
    protected function arrayObjectCast($className, array $arrayOfObject) : array
    {
        $arr = [];
        if (!empty($arrayOfObject)) {
            // verificando o elemento do array
            $obj = $arrayOfObject[0];
            // se for um array associativo converte para stdClass
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
}
