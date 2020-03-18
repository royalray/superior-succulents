<?php

class Db
{
    private $connection;

    function __construct($database_name)
    {
        $this->connection = new mysqli('localhost', 'root', 'NachtSchatten', $database_name);
    }

    function read($table)
    {
        $result = $this->connection->query('select * from ' . $table);
        $array = [];
        while ($row = $result->fetch_assoc()) {
            $array[] = $row;
        }
        return $array;
    }

    function create($table, $insert_array)
    {
        $i=0;
        $columns ='';
        $values = '';
        foreach ($insert_array as $column => $value) {
             $columns.= ($i>0?',' :'').$column;
             $values.= ($i>0?',' :'').'"'.$value.'"';
             $i++;
        }
        $SQL="insert into $table($columns) values ($values)";
        $this->connection->query($SQL);
    }
    function delete($table, $id)
    {
        $this->connection->query("DELETE FROM $table WHERE id = $id");
    }
}


