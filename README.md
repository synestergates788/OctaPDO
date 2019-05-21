# squeedPDO
A light-weight PHP-PDO database library used by squeedPHP framework. This library is inspired by codeigniter active record.

## Getting Started

installing squeedPDO does not require composer, just clone the project and put it in the directory where your site allocated.

### Prerequisites

```
-Php 5.3+
-mysql (any version that supports php 5.3+)
```

## squeedPDO Active Record Documentation

**$this->db->get();** <br />
Runs the selection query and returns the result. Can be used by itself to retrieve all records from a table

```
$query = $this->db->get('mytable');

// Produces: SELECT * FROM mytable
```

The second and third parameters enable you to set a limit and offset clause:
```
$query = $this->db->get('mytable', 10, 20); <br />
// Produces: SELECT * FROM mytable LIMIT 20, 10 (in MySQL. Other databases have slightly different syntax)
```

You'll notice that the above function is assigned to a variable named $query, which can be used to show the results:
```
$query = $this->db->get('mytable');

foreach ($query->result() as $row)
{
    echo $row->title;
}
```
<br />
<br />

**$this->db->select();** <br />
Permits you to write the SELECT portion of your query:

```
$this->db->select('title, content, date');

$query = $this->db->get('mytable');

// Produces: SELECT title, content, date FROM mytable
```
$this->db->select() accepts an optional second parameter. If you set it to FALSE, CodeIgniter will not try to protect your field or table names with backticks. This is useful if you need a compound select statement.
```
$this->db->select('(SELECT SUM(payments.amount) FROM payments WHERE payments.invoice_id=4') AS amount_paid', FALSE);
$query = $this->db->get('mytable');
```
<br />
<br />

**$this->db->join();** <br />
Permits you to write the JOIN portion of your query:

```
$this->db->select('*');
$this->db->from('blogs');
$this->db->join('comments', 'comments.id = blogs.id');

$query = $this->db->get();

// Produces:
// SELECT * FROM blogs
// JOIN comments ON comments.id = blogs.id
```

Multiple function calls can be made if you need several joins in one query. <br />
If you need a specific type of JOIN you can specify it via the third parameter of the function. Options are: left, right, outer, inner, left outer, and right outer.

```
 $this->db->join('comments', 'comments.id = blogs.id', 'left');

// Produces: LEFT JOIN comments ON comments.id = blogs.id
```
<br />
<br />

**$this->db->where();** <br />
This function enables you to set WHERE clauses using one of four methods:

* Simple key/value method: <br />
Notice that the equal sign is added for you.
If you use multiple function calls they will be chained together with AND between them:
	```
	$this->db->where('name', $name);
	$this->db->where('title', $title);
	$this->db->where('status', $status);

	// WHERE name = 'Joe' AND title = 'boss' AND status = 'active' 
	```
* Custom key/value method: <br />
You can include an operator in the first parameter in order to control the comparison:
```
$this->db->where('name !=', $name);
$this->db->where('id <', $id);

// Produces: WHERE name != 'Joe' AND id < 45 
```
* Associative array method: <br />
You can include your own operators using this method as well:
```
$array = array('name' => $name, 'title' => $title, 'status' => $status);

$this->db->where($array);

// Produces: WHERE name = 'Joe' AND title = 'boss' AND status = 'active' 
```

* Custom string: <br />
You can write your own clauses manually:
```
$where = "name='Joe' AND status='boss' OR status='active'";

$this->db->where($where);
```
**$this->db->where()** <br />
accepts an optional third parameter. If you set it to FALSE, CodeIgniter will not try to protect your field or table names with backticks.
<br />
<br />

**$this->db->or_where();** <br />
This function is identical to the one above, except that multiple instances are joined by OR:
```
 $this->db->where('name !=', $name);
$this->db->or_where('id >', $id);

// Produces: WHERE name != 'Joe' OR id > 50
```
<br />
<br />

**$this->db->where_in();** <br />
Generates a WHERE field IN ('item', 'item') SQL query joined with AND if appropriate
```
$names = array('Frank', 'Todd', 'James');
$this->db->where_in('username', $names);
// Produces: WHERE username IN ('Frank', 'Todd', 'James')
```
<br />
<br />

**$this->db->or_where_in();** <br />
Generates a WHERE field IN ('item', 'item') SQL query joined with OR if appropriate
```
$names = array('Frank', 'Todd', 'James');
$this->db->or_where_in('username', $names);
// Produces: OR username IN ('Frank', 'Todd', 'James')
```
<br />
<br />

**$this->db->where_not_in();** <br />
Generates a WHERE field NOT IN ('item', 'item') SQL query joined with AND if appropriate
```
 $names = array('Frank', 'Todd', 'James');
$this->db->where_not_in('username', $names);
// Produces: WHERE username NOT IN ('Frank', 'Todd', 'James')
```
<br />
<br />

**$this->db->or_where_not_in();** <br />
Generates a WHERE field NOT IN ('item', 'item') SQL query joined with OR if appropriate
```
$names = array('Frank', 'Todd', 'James');
$this->db->or_where_not_in('username', $names);
// Produces: OR username NOT IN ('Frank', 'Todd', 'James')
```
<br />
<br />

**$this->db->like();** <br />
This function enables you to generate LIKE clauses, useful for doing searches.

* Simple key/value method: <br />
```
$this->db->like('title', 'match');

// Produces: WHERE title LIKE '%match%' 
```
If you use multiple function calls they will be chained together with AND between them:
```
$this->db->like('title', 'match');
$this->db->like('body', 'match');

// WHERE title LIKE '%match%' AND body LIKE '%match%
```
If you want to control where the wildcard (%) is placed, you can use an optional third argument. Your options are 'before', 'after' and 'both' (which is the default). 
```
$this->db->like('title', 'match', 'before');
// Produces: WHERE title LIKE '%match'

$this->db->like('title', 'match', 'after');
// Produces: WHERE title LIKE 'match%'

$this->db->like('title', 'match', 'both');
// Produces: WHERE title LIKE '%match%' 
```
If you do not want to use the wildcard (%) you can pass to the optional third argument the option 'none'. 
```
$this->db->like('title', 'match', 'none');
// Produces: WHERE title LIKE 'match' 
```
* Associative array method: <br />
```
$array = array('title' => $match, 'page1' => $match, 'page2' => $match);

$this->db->like($array);

// WHERE title LIKE '%match%' AND page1 LIKE '%match%' AND page2 LIKE '%match%'
```
<br />
<br />

**$this->db->or_like();** <br />
This function is identical to the one above, except that multiple instances are joined by OR:
```
$this->db->like('title', 'match');
$this->db->or_like('body', $match);

// WHERE title LIKE '%match%' OR body LIKE '%match%'
```

**$this->db->not_like();** <br />
This function is identical to like(), except that it generates NOT LIKE statements:
```
$this->db->not_like('title', 'match');

// WHERE title NOT LIKE '%match%
```
<br />
<br />

**$this->db->or_not_like();** <br />
This function is identical to not_like(), except that multiple instances are joined by OR:
```
$this->db->like('title', 'match');
$this->db->or_not_like('body', 'match');

// WHERE title LIKE '%match% OR body NOT LIKE '%match%'
```
<br />
<br />

**$this->db->group_by();** <br />
Permits you to write the GROUP BY portion of your query:
```
$this->db->group_by("title");

// Produces: GROUP BY title 
```
You can also pass an array of multiple values as well:
```
$this->db->group_by(array("title", "date"));

// Produces: GROUP BY title, date
```
<br />
<br />

**$this->db->order_by();** <br />
Lets you set an ORDER BY clause. The first parameter contains the name of the column you would like to order by. The second parameter lets you set the direction of the result. Options are asc or desc, or random. 
```
$this->db->order_by("title", "desc");

// Produces: ORDER BY title DESC 
```
You can also pass your own string in the first parameter:
```
$this->db->order_by('title desc, name asc');

// Produces: ORDER BY title DESC, name ASC 
```
Or multiple function calls can be made if you need multiple fields.
```
$this->db->order_by("title", "desc");
$this->db->order_by("name", "asc");

// Produces: ORDER BY title DESC, name ASC 
```
<br />
<br />

**$this->db->insert();** <br />
Generates an insert string based on the data you supply, and runs the query. You can either pass an array or an object to the function. Here is an example using an array:
```
$data = array(
   'title' => 'My title' ,
   'name' => 'My Name' ,
   'date' => 'My date'
);

$this->db->insert('mytable', $data);

// Produces: INSERT INTO mytable (title, name, date) VALUES ('My title', 'My name', 'My date')
```
The first parameter will contain the table name, the second is an associative array of values.
Here is an example using an object:
```
/*
    class Myclass {
        var $title = 'My Title';
        var $content = 'My Content';
        var $date = 'My Date';
    }
*/

$object = new Myclass;

$this->db->insert('mytable', $object);

// Produces: INSERT INTO mytable (title, content, date) VALUES ('My Title', 'My Content', 'My Date')
```
The first parameter will contain the table name, the second is an object.
<br />
<br />

**$this->db->update();** <br />
Generates an update string and runs the query based on the data you supply. You can pass an array or an object to the function. Here is an example using an array:
```
 $data = array(
               'title' => $title,
               'name' => $name,
               'date' => $date
            );

$this->db->where('id', $id);
$this->db->update('mytable', $data);

// Produces:
// UPDATE mytable
// SET title = '{$title}', name = '{$name}', date = '{$date}'
// WHERE id = $id
```
Or you can supply an object:
```
/*
    class Myclass {
        var $title = 'My Title';
        var $content = 'My Content';
        var $date = 'My Date';
    }
*/

$object = new Myclass;

$this->db->where('id', $id);
$this->db->update('mytable', $object);

// Produces:
// UPDATE mytable
// SET title = '{$title}', name = '{$name}', date = '{$date}'
// WHERE id = $id
```
You'll notice the use of the $this->db->where() function, enabling you to set the WHERE clause. You can optionally pass this information directly into the update function as a string:
```
$this->db->update('mytable', $data, "id = 4");
```
Or as an array:
```
$this->db->update('mytable', $data, array('id' => $id));
```
You may also use the $this->db->set() function described above when performing updates.
<br />

**$this->db->delete();** <br />
Generates a delete SQL string and runs the query.
```
$this->db->delete('mytable', array('id' => $id));

// Produces:
// DELETE FROM mytable
// WHERE id = $id
```
The first parameter is the table name, the second is the where clause. You can also use the where() or or_where() functions instead of passing the data to the second parameter of the function:
```
 $this->db->where('id', $id);
$this->db->delete('mytable');

// Produces:
// DELETE FROM mytable
// WHERE id = $id
```
An array of table names can be passed into delete() if you would like to delete data from more than 1 table.
```
$tables = array('table1', 'table2', 'table3');
$this->db->where('id', '5');
$this->db->delete($tables);
```
If you want to delete all data from a table, you can use the truncate() function, or empty_table().
<br />
<br />

**Example Of Queries Using squeedPHP Active Record** <br />
```
Example of query using our custom database library
$select = array(
   "*",
   "u.first_name",
   "u.last_name",
   "u.email",
   "ut.type"
);

$db->select($select);
$db->order_by("u.userID","DESC");
$db->group_by("u.user_type");
$db->join("user_role AS ur","ur.user_role_id=ut.user_role","LEFT");
$db->join("user_type AS ut","ut.user_type_id=u.user_type","LEFT");
$db->get('users AS u');
$result_data = $db->result(); //this will return an array of results
```
<br />
<br />

**LIST OF PRE-DEFINED CLASS** <br />
```
--$db->insert_id() //return the last inserted id
--$db->select()
--$db->where()
--$db->or_where()
--$db->where_in()
--$db->or_where_in()
--$db->where_not_in()
--$db->or_where_not_in()
--$db->order_by()
--$db->group_by()
--$db->join()
--$db->get()
--$db->row()
--$db->num_rows()
--$db->result()
--$db->like()
--$db->or_like()
--$db->not_like()
--$db->or_not_like()
```



## Author

Melquecedec Catang-catang