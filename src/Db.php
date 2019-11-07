<?php
namespace src;
class Db
{
    public $table  = "";

    private $sql;

    private $filed = 'select * ';

    private $where = '';

    private $group = '';

    private $order = '';

    private $or = '';

    public static function table(String $table){
        return new self($table);
    }

    private function __construct($table){
        $this->table = $table;
        $this->sql  = " from {$table}";
        return $this;
    }

    public function select(){
        $where = !empty($this->where) ? " WHERE ({$this->where})" :'';
        $or    = !empty($this->or) ? " OR ({$this->or})" :'';
        $sql = $this->filed.$this->sql.$where.$or.$this->group.$this->order;
        return (new Model)->query($sql);
    }

    public function where(Array $where){
        $i = 0;
        foreach ($where as $key => $value) {

            if($i != 0 ){
                $this->or .=' AND';
            }

            if(is_array($value)){
                $this->where .= " {$key}{$value[0]}{$value[1]}";
            }else
                $this->where .= " {$key}={$value}";

            ++$i;
        }
        return $this;

    }

    public function filed(String $filed){

        $this->filed = 'select '.trim($filed);
        return $this;
    }

    public function group(String $group){

        $this->group = ' GROUP BY '.$group;
        return $this;
    }

    public function order(String $order){

        $this->order = ' ORDER BY '.$order;
        return $this;
    }


    public function or($where){
        $i = 0;
        foreach ($where as $key => $value) {
            if($i != 0 ){
                $this->or .=' AND';
            }

            if(is_array($value)){
                $this->or .= " {$key}{$value[0]}{$value[1]}";
            }
            $this->or .= " {$key}={$value}";
            $i++;
        }
        return $this;
    }
}