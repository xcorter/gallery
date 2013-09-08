<?php
require_once('config.php');

class DB {
    var $select; // what select
    var $table; // which table
    var $condition; // sql condiction
    var $link; // db link
    var $value;
    var $fields;
    var $setup;

    function __construct() {
        $cfg = new Config();
        $this->link = mysqli_connect($cfg->host, $cfg->db_user, $cfg->db_pass, $cfg->db_name);
        if (!$this->link) { 
            printf("Could not connect to DB. Error: %s\n", mysqli_connect_error()); 
            exit; 
        }
    }
    
    function setTable($table) {
        $this->table = mysqli_real_escape_string($this->link, $table);
    }
    
    function setSelect($select) {
        $this->select = mysqli_real_escape_string($this->link, $select);
    }
    
    function setValues($values) {
        $this->value = '';
        $this->fields = '';
        $i = 0;
        foreach ($values as $key => $value) {
            $this->value .= '"'.mysqli_real_escape_string($this->link, trim($value)).'"';
            $this->fields .= $key;
            $i++;
            if ($i != count($values)) {
                $this->value .= ', ';
                $this->fields .= ', ';
            }
        }
    }
    
    // function for set condition (WHERE id=5), 
    // $setup = 1, where we use UPDATE
    // $orand is used for "WHERE id = foo AND username=bar" or "WHERE id = foo OR username=bar"
    function setCondition($conditions, $setup = 0, $orand = "and"){
        $i = 0;
        if ($setup) $separator = ', ';
        else if ($orand == "and") {
            $separator = ' and ';
        } else {
            $separator = ' or ';
        }
          
        foreach ($conditions as $key => $value) {
            $temp .= mysqli_real_escape_string($this->link, $key).'="'.mysqli_real_escape_string($this->link, trim($value)).'" ';
            $i++;
            if ($i != count($conditions)) $temp .= $separator;
        }
        if ($setup) {
            $this->setup = $temp;
        } else {
            $this->condition = 'WHERE '.$temp;
        }
    }
    
    function insert() {
        $query = 'INSERT INTO '.$this->table.' ('.$this->fields.') VALUES ('.$this->value.')';
        if (mysqli_query($this->link, $query)) {
            return mysqli_insert_id($this->link);
        } else {
            return false;
        }
    }
    
    function update() {
        $query = 'UPDATE '.$this->table.' SET '.$this->setup.' '.$this->condition;
        if (mysqli_query($this->link, $query)) {
            return true;
        } else {
            return false;
        }
    }
    
    function delete() {
        $query = 'DELETE FROM '.$this->table.' '.$this->condition;
        if (mysqli_query($this->link, $query)) {
            return true;
        } else {
            return false;
        }
    }
    
    function runQuery($query) {
        if ($result = mysqli_query($this->link, $query)) { 
            $arr = array();
            while ($row = mysqli_fetch_assoc($result)) {
                array_push($arr, $row);
            }
            
            return $arr;
        } else {
            return false;
        }
    }
    
    function getResult(){
        $query = 'SELECT '.$this->select.' FROM '.$this->table.' '.$this->condition;
        if ($result = mysqli_query($this->link, $query)) { 
            $arr = array();
            while ($row = mysqli_fetch_assoc($result)) {
                array_push($arr, $row);
            }
            return $arr;
        } else {
            return false;
        }
    }
}
?>