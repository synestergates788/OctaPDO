<?php

class OctaPDO extends Controller{
    var $con;
    var $field;
    var $db;

    var $get;
    var $select;
    var $where;
    var $or_where;
    var $order_by;
    var $join;
    var $group_by;
    var $table;
    var $limit;
    var $offset;
    var $last_id;

    var $where_in;
    var $or_where_in;
    var $where_not_in;
    var $or_where_not_in;

    var $like;
    var $or_like;
    var $not_like;
    var $or_not_like;

    public function __construct($DB_con){
        $this->db = $DB_con;
    }

    public function insert($array,$table){
        /*clear init variable*/
        $this->last_id = '';
        /*end clearing init variables*/

        try{
            $table_fields = [];
            $table_fields_val = [];
            if($array){
                foreach($array as $row=>$val){
                    $table_fields[] = $row;
                    $table_fields_val[] = "'".$val."'";
                }

                $sql_field = implode($table_fields, ",");
                $sql_field_val = implode($table_fields_val, ",");

                if($sql_field && $sql_field_val){
                    $sqls = $this->db->prepare("INSERT into {$table}({$sql_field}) VALUE ({$sql_field_val})");
                    if($sqls->execute()){
                        #$this->last_id = $sqls->lastInsertId();
                        return true;
                    }else{
                        return false;
                    }

                }else{
                    //pdo error
                    return false;
                }
            }

        }catch(PDOException $e){
            echo $e->getMessage().'<br><br>';
        }
    }
	
	public function insert_batch($array,$table){
        /*clear init variable*/
        $this->last_id = '';
        /*end clearing init variables*/

        try{
            if($array){
                foreach($array as $row_ib){
                    $table_fields = [];
                    $table_fields_val = [];
                    if($row_ib){
                        foreach($row_ib as $row=>$val){
                            $table_fields[] = $row;
                            $table_fields_val[] = "'".$val."'";
                        }

                        $sql_field = implode($table_fields, ",");
                        $sql_field_val = implode($table_fields_val, ",");

                        if($sql_field && $sql_field_val){
                            $sqls = $this->db->prepare("INSERT into {$table}({$sql_field}) VALUE ({$sql_field_val})");
                            if(!$sqls->execute()){
                                return false;
                            }

                        }else{
                            //pdo error
                            return false;
                        }
                    }
                }
            }

            return true;

        }catch(PDOException $e){
            echo $e->getMessage().'<br><br>';
        }
    }

    public function update($data,$where,$table){
        $table_fields = [];
        $table_fields_val = [];

        $where_fields = [];
        $where_fields_val = [];

        try{
            if($where){
                foreach($where as $key=>$row_w){
                    $where_fields[] = $key."='".$row_w."'";
                    $where_fields_val[] = $row_w;
                }
            }

            if($data){
                foreach($data as $row=>$val){
                    $table_fields[] = $row."='".$val."'";
                    $table_fields_val[] = "'".$val."'";
                }
            }

            $sql_field = implode($table_fields, ",");
            $sql_where_field = implode($where_fields, ",");

            if($sql_field && $sql_where_field){
                $sqls = $this->db->prepare("UPDATE {$table} SET {$sql_field} WHERE {$sql_where_field}");
                if($sqls->execute()){
                    return true;
                }else{
                    return false;
                }

            }else{
                //pdo error
                return false;
            }

        }catch(PDOException $e){
            echo $e->getMessage().'<br><br>';
        }
    }
	
	public function update_batch($table,$data,$where){
        try{
            if($data){
                foreach($data as $key_ub=>$row_ub){
                    $table_fields = [];
                    $where_fields = [];

                    if($row_ub){
                        foreach($row_ub as $key_ub_data=>$row_ub_data){
                            if($key_ub_data == $where){
                                $where_fields[] = $key_ub_data."='".$row_ub_data."'";

                            }else{
                                $table_fields[] = $key_ub_data."='".$row_ub_data."'";
                            }
                        }
                    }

                    $sql_field = implode($table_fields, ",");
                    $sql_where_field = implode($where_fields, ",");

                    if($sql_field && $sql_where_field){
                        $sqls = $this->db->prepare("UPDATE {$table} SET {$sql_field} WHERE {$sql_where_field}");
                        if(!$sqls->execute()){
                            return false;
                        }

                    }else{
                        //pdo error
                        return false;
                    }
                }
            }

        }catch(PDOException $e){
            echo $e->getMessage().'<br><br>';
        }
    }

    public function delete($where,$table){
        $where_fields = [];

        if($where){
            foreach($where as $key=>$row_w){
                if($row_w){
                    foreach($row_w as $row_id){
                        $where_fields[] = $key."='".$row_id."'";

                        $sqls = $this->db->prepare("DELETE FROM {$table} WHERE {$key}='{$row_id}'");
                        $sqls->execute();
                    }
                }
            }
        }

        return true;
    }

    public function insert_id(){
        return $this->last_id;
    }

    /*this is for join queries*/
    public function select($data){
        $reset = [];
        if($data){
            foreach($data as $key=>$row){
                array_push($reset,$row);
            }
        }

        $this->select = $reset;
        return $this->select;
    }

    public function where($data=null,$match=null){
        $tmp_where = '';
        $arr_check = false;
        if($data){
            if(is_array($data)){
                end($data);
                $last_element = key($data);

                if($data){
                    $arr_check = true;
                    foreach($data as $key=>$row){
                        if($key == $last_element){
                            $tmp_where .= $key."='".$row."'";
                        }else{
                            $tmp_where .= $key."='".$row."' AND ";
                        }
                    }
                }
            }else{
                $arr_check = false;
                $tmp_where = "WHERE ".$data."='".$match."'";
            }
        }

        $this->where = ($arr_check == false) ? $tmp_where : "WHERE ".$tmp_where;
    }

    public function or_where($data=null,$match=null){
        $tmp_or_where = '';
        $arr_check = false;
        if($data){
            if(is_array($data)){
                end($data);
                $last_element = key($data);
                $arr_check = true;

                if($data){
                    foreach($data as $key=>$row){
                        if($key == $last_element){
                            $tmp_or_where .= $key."='".$row."'";
                        }else{
                            $tmp_or_where .= $key."='".$row."' AND ";
                        }
                    }
                }
            }else{
                $arr_check = false;
                $tmp_or_where = "OR ".$data."='".$match."'";
            }
        }

        $this->or_where = ($arr_check == false) ? $tmp_or_where : "OR ".$tmp_or_where;
    }

    public function where_in($field,$data){
        $where_in_fields = '';
        $last_key = end(array_keys($data));
        if($data){
            foreach($data as $key=>$row){
                if($key == $last_key){
                    $where_in_fields .= $row;
                }else{
                    $where_in_fields .= $row.",";
                }
            }
        }

        $this->where_in = 'WHERE '.$field.' IN ('.$where_in_fields.')';
    }

    public function or_where_in($field,$data){
        $where_in_fields = '';
        $last_key = end(array_keys($data));
        if($data){
            foreach($data as $key=>$row){
                if($key == $last_key){
                    $where_in_fields .= $row;
                }else{
                    $where_in_fields .= $row.",";
                }
            }
        }

        $this->or_where_in = 'OR '.$field.' IN ('.$where_in_fields.')';
    }

    public function where_not_in($field,$data){
        $where_in_fields = '';
        $last_key = end(array_keys($data));
        if($data){
            foreach($data as $key=>$row){
                if($key == $last_key){
                    $where_in_fields .= $row;
                }else{
                    $where_in_fields .= $row.",";
                }
            }
        }

        $this->where_not_in = 'WHERE '.$field.' NOT IN ('.$where_in_fields.')';
    }

    public function or_where_not_in($field,$data){
        $where_in_fields = '';
        $last_key = end(array_keys($data));
        if($data){
            foreach($data as $key=>$row){
                if($key == $last_key){
                    $where_in_fields .= $row;
                }else{
                    $where_in_fields .= $row.",";
                }
            }
        }

        $this->or_where_not_in = 'OR '.$field.' NOT IN ('.$where_in_fields.')';
    }

    public function like($data=null,$match=null){
        $tmp_like = '';
        $arr_check = false;
        if($data){
            if(is_array($data)){
                end($data);
                $last_element = key($data);

                if($data){
                    $arr_check = true;
                    foreach($data as $key=>$row){
                        if($key == $last_element){
                            $tmp_like .= $key." LIKE '%".$row."%'";
                        }else{
                            $tmp_like .= $key." LIKE '%".$row."%' AND ";
                        }
                    }
                }
            }else{
                $arr_check = false;
                $tmp_like = "WHERE ".$data." LIKE '%".$match."%'";
            }

        }

        $this->like = ($arr_check == false) ? $tmp_like : "WHERE ".$tmp_like;
    }

    public function or_like($data=null,$match=null){
        $tmp_or_like = '';
        if($data){
            if(is_array($data)){
                end($data);
                $last_element = key($data);

                if($data){
                    foreach($data as $key=>$row){
                        if($key == $last_element){
                            $tmp_or_like .= "OR ".$key." LIKE '%".$row."%'";
                        }else{
                            $tmp_or_like .= "OR ".$key." LIKE '%".$row."%' AND ";
                        }
                    }
                }
            }else{
                $tmp_or_like = "OR ".$data." LIKE '%".$match."%'";
            }
        }

        $this->or_like = $tmp_or_like;
    }

    public function not_like($data=null,$match=null){
        $tmp_like = '';
        $arr_check = false;
        if($data){
            if(is_array($data)){
                end($data);
                $last_element = key($data);

                if($data){
                    $arr_check = true;
                    foreach($data as $key=>$row){
                        if($key == $last_element){
                            $tmp_like .= $key." NOT LIKE '%".$row."%'";
                        }else{
                            $tmp_like .= $key." NOT LIKE '%".$row."%' AND ";
                        }
                    }
                }
            }else{
                $arr_check = false;
                $tmp_like = "WHERE ".$data." NOT LIKE '%".$match."%'";
            }

        }

        $this->like = ($arr_check == false) ? $tmp_like : "WHERE ".$tmp_like;
    }

    public function or_not_like($data=null,$match=null){
        $tmp_or_like = '';
        if($data){
            if(is_array($data)){
                end($data);
                $last_element = key($data);

                if($data){
                    foreach($data as $key=>$row){
                        if($key == $last_element){
                            $tmp_or_like .= "OR ".$key." NOT LIKE '%".$row."%'";
                        }else{
                            $tmp_or_like .= "OR ".$key." NOT LIKE '%".$row."%' AND ";
                        }
                    }
                }
            }else{
                $tmp_or_like = "OR ".$data." NOT LIKE '%".$match."%'";
            }
        }

        $this->or_like = $tmp_or_like;
    }

    public function order_by($field,$sort){
        $this->order_by = $field.' '.$sort;
    }

    public function join($table,$joint,$join_type="INNER"){
        $this->join = $join_type.' JOIN '.$table.' ON '.$joint;
    }

    public function group_by($field){
        $this->group_by = 'GROUP BY '.$field;
    }

    public function get($table,$limit=null,$offset=null){
        $this->table = $table;
        $this->limit = $limit;
        $this->offset = $offset;

    }

    public function clear_global_var(){
        $this->select = '';
        $this->where = '';
        $this->or_where = '';
        $this->order_by = '';
        $this->join = '';
        $this->group_by = '';
        $this->limit = '';
        $this->table = '';
        $this->offset = '';
        $this->where_in = '';
        $this->or_where_in = '';
        $this->where_not_in = '';
        $this->or_where_not_in = '';
        $this->like = '';
        $this->or_like = '';
        $this->not_like = '';
        $this->or_not_like = '';
    }

    public function row(){
        /*init var*/
        $result = [];
        $select = ($this->select) ? implode(',',$this->select) : '*';
        $order_by = ($this->order_by) ? 'ORDER BY '.$this->order_by : '';
        $join = ($this->join) ? $this->join : '';
        $group_by = ($this->group_by) ? $this->group_by : '';
        $limit = ($this->limit) ? 'LIMIT '.$this->limit : '';
        $table = ($this->table) ? $this->table : '';

        $where = ($this->where) ? $this->where : '';
        $or_where = ($this->or_where) ? $this->or_where : '';
        $where_in = ($this->where_in) ? $this->where_in : '';
        $or_where_in = ($this->or_where_in) ? $this->or_where_in : '';
        $where_not_in = ($this->where_not_in) ? $this->where_not_in : '';
        $or_where_not_in = ($this->or_where_not_in) ? $this->or_where_not_in : '';

        $like = ($this->like) ? $this->like : '';
        $or_like = ($this->or_like) ? $this->or_like : '';
        $not_like = ($this->not_like) ? $this->not_like : '';
        $or_not_like = ($this->or_not_like) ? $this->or_not_like : '';
        /*end init var*/

        try{
            $sql = $this->db->prepare("SELECT {$select} FROM {$table} {$join} {$where} {$or_where} {$where_in} {$or_where_in} {$where_not_in} {$or_where_not_in} {$like} {$or_like} {$not_like} {$or_not_like} {$group_by} {$order_by} {$limit}");
            $sql->execute();
            $tmp_result = $sql->fetch();
            if($tmp_result){
                foreach($tmp_result as $key=>$row){
                    $result[$key] = $row;
                }
            }

            /*clear global variables*/
            $this->clear_global_var();
            /*end clearing global variables*/

            return $result;

        }catch(PDOException $e){
            echo $e->getMessage().'<br><br>';
            echo "SELECT {$select} FROM {$table} {$join} {$where} {$or_where} {$where_in} {$or_where_in} {$where_not_in} {$or_where_not_in} {$like} {$or_like} {$not_like} {$or_not_like} {$group_by} {$order_by} {$limit}";
        }
    }

    public function num_rows(){
        /*init var*/
        $result = 0;
        $select = ($this->select) ? implode(',',$this->select) : '*';
        $order_by = ($this->order_by) ? 'ORDER BY '.$this->order_by : '';
        $join = ($this->join) ? $this->join : '';
        $group_by = ($this->group_by) ? $this->group_by : '';
        $limit = ($this->limit) ? 'LIMIT '.$this->limit : '';
        $table = ($this->table) ? $this->table : '';

        $where = ($this->where) ? $this->where : '';
        $or_where = ($this->or_where) ? $this->or_where : '';
        $where_in = ($this->where_in) ? $this->where_in : '';
        $or_where_in = ($this->or_where_in) ? $this->or_where_in : '';
        $where_not_in = ($this->where_not_in) ? $this->where_not_in : '';
        $or_where_not_in = ($this->or_where_not_in) ? $this->or_where_not_in : '';

        $like = ($this->like) ? $this->like : '';
        $or_like = ($this->or_like) ? $this->or_like : '';
        $not_like = ($this->not_like) ? $this->not_like : '';
        $or_not_like = ($this->or_not_like) ? $this->or_not_like : '';
        /*end init var*/

        try{
            $sql = $this->db->prepare("SELECT {$select} FROM {$table} {$join} {$where} {$or_where} {$where_in} {$or_where_in} {$where_not_in} {$or_where_not_in} {$like} {$or_like} {$not_like} {$or_not_like} {$group_by} {$order_by} {$limit}");
            $sql->execute();
            $result = $sql->rowCount();

            /*clear global variables*/
            $this->clear_global_var();
            /*end clearing global variables*/

            return $result;

        }catch(PDOException $e){
            echo $e->getMessage().'<br><br>';
            echo "SELECT {$select} FROM {$table} {$join} {$where} {$or_where} {$where_in} {$or_where_in} {$where_not_in} {$or_where_not_in} {$like} {$or_like} {$not_like} {$or_not_like} {$group_by} {$order_by} {$limit}";
        }
    }

    public function result(){
        /*init var*/
        $result = array();
        $select = ($this->select) ? implode(',',$this->select) : '*';
        $order_by = ($this->order_by) ? 'ORDER BY '.$this->order_by : '';
        $join = ($this->join) ? $this->join : '';
        $group_by = ($this->group_by) ? $this->group_by : '';
        $limit = ($this->limit) ? 'LIMIT '.$this->limit : '';
        $table = ($this->table) ? $this->table : '';

        $where = ($this->where) ? $this->where : '';
        $or_where = ($this->or_where) ? $this->or_where : '';
        $where_in = ($this->where_in) ? $this->where_in : '';
        $or_where_in = ($this->or_where_in) ? $this->or_where_in : '';
        $where_not_in = ($this->where_not_in) ? $this->where_not_in : '';
        $or_where_not_in = ($this->or_where_not_in) ? $this->or_where_not_in : '';

        $like = ($this->like) ? $this->like : '';
        $or_like = ($this->or_like) ? $this->or_like : '';
        $not_like = ($this->not_like) ? $this->not_like : '';
        $or_not_like = ($this->or_not_like) ? $this->or_not_like : '';
        /*end init var*/

        try{
            $sql = $this->db->prepare("SELECT {$select} FROM {$table} {$join} {$where} {$or_where} {$where_in} {$or_where_in} {$where_not_in} {$or_where_not_in} {$like} {$or_like} {$not_like} {$or_not_like} {$group_by} {$order_by} {$limit}");
            $sql->execute();
            $tmp_result = $sql->setFetchMode(PDO::FETCH_ASSOC);
            if($tmp_result){
                foreach($sql->fetchAll() as $key=>$row){
                    $result[$key] = (object)$row;
                }
            }

            /*clear global variables*/
            $this->clear_global_var();
            /*end clearing global variables*/

            return $result;

        }catch (PDOException $e){
            echo $e->getMessage().'<br><br>';
            echo "SELECT {$select} FROM {$table} {$join} {$where} {$or_where} {$where_in} {$or_where_in} {$where_not_in} {$or_where_not_in} {$like} {$or_like} {$not_like} {$or_not_like} {$group_by} {$order_by} {$limit}";
        }
    }
    /*end for joint queries*/
}