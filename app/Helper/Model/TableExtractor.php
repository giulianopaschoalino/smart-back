<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Helper\Model;

use Illuminate\Support\Facades\DB;

/**
 * Description of TableExtractor
 *
 * @author renan
 */
class TableExtractor
{
    
    /**
     * @var string
     */
    private $tableName;
    
    /**
     * @var string
     */
    private $owner;
    
    /**
     * Default empty space
     * @var type
     */
    private $e = "    ";

    protected $attributes = [];


    private $_sql_filterExtractor = "SELECT  
                                        COLUMN_NAME,
                                        case when DATA_TYPE = 'NUMBER' THEN
                                            decode(data_scale,0,'INTEGER','FLOAT')
                                        when DATA_TYPE = 'DATE' THEN
                                            'DATE'
                                        else
                                            'STRING'
                                        end as DATA_TYPE
                                    FROM all_tab_columns where table_name = ?
                                    and owner = ?
                                    order by column_id";
    
    public function __construct(string $tableName = '', string $owner = 'KDB1')
    {
        $this->setTable($tableName);
        $this->setOwner($owner);
    }
    
    public function getAttributes() : array
    {
        if (count($this->attributes) === 0) {
            $result =  DB::select(DB::raw($this->_sql_filterExtractor), array(
                $this->tableName,
                $this->owner,
              ));
            foreach ($result as $r) {
                $r->column_name = strtolower($r->column_name);
                $r->data_type = strtolower($r->data_type);
            }
            $this->attributes = $result;
        }
        return $this->attributes;
    }
    
    public function toPhpProperties() : array
    {
        $attributes = $this->getAttributes();
        return array_map(function ($r) {
            return
            "{$this->e}/**". PHP_EOL .
            "{$this->e}* @var ".$this->getDataTypePhp($r->data_type). PHP_EOL.
            "{$this->e}*/" . PHP_EOL .
            "{$this->e}protected $" . strtolower($r->column_name).';' . PHP_EOL;
        }, $attributes);
    }

    public function toSetters() : array
    {
        $attributes = $this->getAttributes();
        
        return array_map(function ($r) {
            return
            "{$this->e}public function set" . ucfirst($r->column_name) . "(" . $this->getDataTypePhp($r->data_type) . " $".$r->column_name.")" . PHP_EOL .
            "{$this->e}{" . PHP_EOL .
            "{$this->e}{$this->e}\$this->".strtolower($r->column_name). " = $" . $r->column_name . ";" . PHP_EOL .
            "{$this->e}}" . PHP_EOL;
        }, $attributes);
    }
    
    public function toGetters() : array
    {
        $attributes = $this->getAttributes();
        
        return array_map(function ($r) {
            return
            "{$this->e}public function get" . ucfirst($r->column_name) . "()" . $this->getDataTypePhp($r->data_type, true) . PHP_EOL .
            "{$this->e}{" . PHP_EOL .
            "{$this->e}{$this->e}return \$this->".strtolower($r->column_name). ";" . PHP_EOL .
            "{$this->e}}" . PHP_EOL;
        }, $attributes);
    }
        
    public function toSwaggerAnnotation($entityName = 'Entity')
    {
        $swagger = "";
        $attributes = $this->getAttributes();
        $swagger.='/**' . PHP_EOL;
        $swagger.=' * @OA\Schema(' . PHP_EOL;
        $swagger.=' *      schema="'.$entityName.'",' . PHP_EOL;
        $swagger.=' *      type="object",' . PHP_EOL;
        $swagger.=' *      description="'.$entityName.' entity",' . PHP_EOL;
        $swagger.=' *      title="'.$entityName.' entity",' . PHP_EOL;
        foreach ($attributes as $at) {
            $type = 'type="'.$at->data_type.'"';
            if ($at->data_type === 'float') {
                $type = 'type="number", format="float", example="15.99"';
            }
            if ($at->data_type === 'date') {
                $type = 'type="string", format="date", example="dd/mm/YYYY"';
            }
            $swagger.=' *      @OA\Property(property="'.$at->column_name.'", '.$type.', description="'.ucfirst($at->column_name).'"),' . PHP_EOL;
        }
        $swagger.=" * )" . PHP_EOL;
        $swagger.=" */";
        return $swagger;
    }

    public function getDataTypePhp($dataType, $return = false)
    {
        $valor = "";
        switch ($dataType):
            case 'string':
                $valor = 'string';
        break;
        case 'float':
                $valor = 'float';
        break;
        case 'integer':
                $valor = 'int';
        break;
        case 'array':
                $valor = 'array';
        break;
        default:
                $valor = '';
        break;
        endswitch;
        
        if ($return === true && !empty($valor)) {
            $valor = ' : ' . $valor;
        }
        return $valor;
    }
    
    protected function setTable($table)
    {
        $this->tableName = strtoupper($table);
    }
    
    protected function setOwner($owner)
    {
        $this->owner = strtoupper($owner);
    }
    
    protected function createColumn($columName, $dataType) : \stdClass
    {
        $obj = new \stdClass();
        $obj->column_name =  strtolower($columName);
        $obj->data_type = strtolower($dataType);
        return $obj;
    }
    
    protected function getDataTypeByValue($value)
    {
        // verificando se é numerico
        if (is_object($value)===false && is_numeric(str_replace(',', '.', $value))) {
            // verificando se é float
            if (strpos($value, ",")!==false || strpos($value, ".")!==false) {
                return "float";
            } else {
                return "integer";
            }
        } elseif (is_string($value)) {
            return "string";
        } elseif (is_array($value)) {
            return "array";
        } else {
            return "";
        }
    }
}
