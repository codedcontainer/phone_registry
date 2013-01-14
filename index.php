<!DOCTYPE html>
<head>
	<title>Phone Registry</title>
	<link type="text/css" rel="stylesheet" href="index.css"/>
	<link href='http://fonts.googleapis.com/css?family=Dosis:200' rel='stylesheet' type='text/css'>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
<div id="container">
	<h1>Phone Registry</h1>
	<h3>Hello and welcome to our phone registry. Below are the following options:</h3>
	<div id="go">
	<a href="?id=add">Add New</a>
	<a href="?id=modify">Modify Exsisting</a>
	<a href="?id=view">View All Records</a>
	</div>
<br/>
	<?php
	error_reporting(0);
			include('con.php');
			mysql_select_db('jutt_db');
	
		ctb();
							//Gets the id value from url assosiated with link
		$type=$_GET['id'];
							//Depending on link clicked, specified function
							//will be initialized.
		switch ($type)
		{
			case 'add':
				add();
				break;
			case 'modify':
				modi();
				break;
			case 'view':
				view();
			default:
				break;
		}
		
		function add()
		{
			if(filter_has_var(INPUT_POST,'submit'))
			{
						//Retrieve data from forms.	
				print("<p>Your data has been submitted!</p>");
				$fname=$_POST['fname'];
				$lname=$_POST['lname'];
				$phone_id=$_POST['phone_id'];
				$phone_number=$_POST['phone_number'];
				$type=$_POST['type'];
				$name_id=$_POST['name_id'];
						//Current number of items in names table
						//If list of phone numbers is not selected
						//place new number into database
				if($phone_id=='')
				{
					mysql_query("INSERT INTO phone_number VALUES
					(
						'',
						'$phone_number',
						'$type'
					)");
					$phone_id = mysql_insert_id();
				}
									//if list of names is not selected place new name
									//into database
				if($name_id=='')
				{
					mysql_query("INSERT INTO names VALUES
					(
						'',
						'$fname',
						'$lname'
					)
					");	
					$name_id=mysql_insert_id();
				}
			
				mysql_query("INSERT INTO name_num VALUES ('',$name_id,$phone_id)");
			}
			else 
			{						//If the user did not click on the submit button
									//the following table will show up.
				$add=<<<CREATE
				<form method="post" action="">
				<p>Select Name:</p>
CREATE;
				$select=mysql_query("SELECT * FROM names") or die(mysql_error());
				$add.= "<select name='name_id'>";
				$add.="<option selected></option>";
				while($rows=mysql_fetch_array($select))
				{
					
					$add.= "<option value="."'".$rows['name_id']."'".">";
					$add.= $rows['first']." ".$rows['last'];
					$add.= "</option>";
				}
				$add.= "</select>";
				$add.=<<<CREATE
				<p>Or Add Name:
				<fieldset>First Name:<input type="text" name="fname"/></fieldset>
				<fieldset>Last Name:<input type="text" name="lname"/></fieldset>
				<hr>
				<p>Select Number</p>	
CREATE;
									//display a list of numbers already entered
									//into the database.
				$select=mysql_query("SELECT * FROM phone_number") or die(mysql_error());
				$add.= "<select name='phone_id'>";
				$add.="<option selected></option>";
				while($rows=mysql_fetch_array($select))
				{
					$add.= "<option value="."'".$rows['num_id']."'".">";
					$add.= $rows['num'];
					$add.= "</option>";
				}
				$add.= "</select>";
									//select 'type' values corrispond with phone_type table.
				$add.=<<<CREATE
				<p>Or Add Number:</p>
				<input type="tel" name='phone_number' id='numb'/>
				<p>Add Phone Type:</p>
				<select name='type' id='phone_type'>
					<option value="1">mobile</option>
					<option value="2">work</option>
					<option value="3">home</option>
				</select>
				<br/>
				<br/>
				<input type="submit" name='submit' id='submit'/>
				</form>
CREATE;
				print $add;
			}
		}
		function modi()
		{
			$select=mysql_query("SELECT names.name_id,phone_number.num_id,name_num.name_num_id,names.first,names.last, phone_number.num,phone_type.type 
								FROM names,name_num,phone_number,phone_type 
								WHERE names.name_id=name_num.name_id 
								AND phone_number.num_id=name_num.num_id 
								AND phone_type.type_id=phone_number.type_id");
			
			$table="<table border='1'>";
			$table.=<<<TABLE
			<tr>
				<th>First</th>
				<th>Last</th>
				<th>Phone</th>
				<th>Type</th>
			</tr>
TABLE;
			while($rows=mysql_fetch_array($select))
			{
				$table.="<tr>";
				$table.="<td>".$rows['first']."</td>";
				$table.="<td>".$rows['last']."</td>";
				$table.="<td>".$rows['num']."</td>";
				$table.="<td>".$rows['type']."</td>";
				$table.="<td>"."<a href='?id=modify&nameid=".$rows['name_id']."'><img src='images/userdelete.png'  alt='delete'  class='img'/</a>";
				$table.="<td>"."<a href='?id=modify&namenum=".$rows['name_num_id']."'><img src='images/phonedelete.png'  alt='delete'  class='img'/></a>";
				$table.="<td>"."<a href='?id=modify&numid=".$rows['num_id']."'><img src='images/delete.png' alt='delete' class='img'/></a>";
				
			}
			$table.="</table>";
			print $table;	
			$del=$_GET['numid'];
			$name=$_GET['nameid'];
			$all=$_GET['namenum'];
			
			if(isset($name))
			{
				mysql_query("DELETE FROM names WHERE name_id=$name");
			}
					//Deletes phone number from phone number table.
			if(isset($del))
			{				
				$sql="DELETE FROM phone_number WHERE num_id=$del";
				mysql_query($sql);
			}
			if(isset($all))
			{
				print "hi";
				mysql_query("DELETE FROM name_num WHERE name_num_id=$all");
			}
		}
		function view()
		{						//Peieces together all tables and places data into table.
			$select=mysql_query("SELECT names.first,names.last, phone_number.num,phone_type.type 
			FROM names,name_num,phone_number,phone_type WHERE names.name_id=name_num.name_id 
			AND phone_number.num_id=name_num.num_id 
			AND phone_type.type_id=phone_number.type_id");
			
			$table="<table border='1'>";
			$table.=<<<TABLE
			<tr>
				<th>First</th>
				<th>Last</th>
				<th>Phone</th>
				<th>Type</th>
			</tr>
TABLE;
			
			while($rows=mysql_fetch_array($select))
			{
				$table.="<tr>";
				$table.="<td>".$rows['first']."</td>";
				$table.="<td>".$rows['last']."</td>";
				$table.="<td>".$rows['num']."</td>";
				$table.="<td>".$rows['type']."</td>";
			}
			$table.="</table>";
			print $table;	
		}
							//Creates new tables if need to be created.
		function ctb()
		{
								//first and last names.
			mysql_query("CREATE TABLE names
			(
				`name_id` INT AUTO_INCREMENT,
				`first` VARCHAR(15),
				`last` VARCHAR(15),
				PRIMARY KEY(`name_id`)
			)");
								//phone type.
			mysql_query("CREATE TABLE phone_type(
				`type_id` INT AUTO_INCREMENT,
				`type` VARCHAR(10),
				PRIMARY KEY(`type_id`)
			)");
								//phone numbers.
			mysql_query("CREATE TABLE phone_number(
				`num_id` INT AUTO_INCREMENT,
				`num` INT(10),
				`type_id` INT,
				PRIMARY KEY(`num_id`)
				
			)");
								//relational for phone #'s and names.
			mysql_query("CREATE TABLE name_num(
				`name_num_id` INT AUTO_INCREMENT,
				`name_id` INT,
				`num_id` INT,
				PRIMARY KEY(`name_num_id`)
			)");	
		}
	?>
</div>
</body>
</html>