<!--Test Oracle file for UBC CPSC304 2011 Winter Term 2
  Created by Jiemin Zhang
  Modified by Simona Radu
  This file shows the very basics of how to execute PHP commands
  on Oracle.
  specifically, it will drop a table, create a table, insert values
  update values, and then query for values

  IF YOU HAVE A TABLE CALLED "tab1" IT WILL BE DESTROYED

  The script assumes you already have a server set up
  All OCI commands are commands to the Oracle libraries
  To get the file to work, you must place it somewhere where your
  Apache server can run it, and you must rename it to have a ".php"
  extension.  You must also change the username and password on the
  OCILogon below to be your ORACLE username and password -->

<html>


<head>
    <meta name="google-signin-scope" content="profile email">
    <meta name="google-signin-client_id" content="822842326093-oo9m0j9se9020sqt97q0hf26rq3uqf37.apps.googleusercontent.com">
    <script src="https://apis.google.com/js/platform.js" async defer></script>


  </head>
<div id="welcome"></div>
<p>If you wish to reset the table press on the reset button. If this is the first time you're running this page, you MUST use reset</p>
<form method="POST" action="oracle-test.php">

<p><input type="submit" value="Reset" name="reset"></p>
</form>

<p>Insert values into tab1 below:</p>
<p><font size="2"> Number&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
Name</font></p>
<form method="POST" action="oracle-test.php">
<!--refresh page when submit-->

   <p><input type="text" name="insNo" size="6"><input type="text" name="insName"
size="18">
<!--define two variables to pass the value-->

<input type="submit" value="insert" name="insertsubmit"></p>
</form>
<!-- create a form to pass the values. See below for how to
get the values-->

<p> Update the name by inserting the old and new values below: </p>
<p><font size="2"> Old Name&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
New Name</font></p>
<form method="POST" action="oracle-test.php">
<!--refresh page when submit-->

   <p><input type="text" name="oldName" size="6"><input type="text" name="newName"
size="18">
<!--define two variables to pass the value-->

<input type="submit" value="update" name="updatesubmit"></p>
<input type="submit" value="run hardcoded queries" name="dostuff"></p>
</form>

<script>
  function signOut() {
	var auth2 = gapi.auth2.getAuthInstance();
    auth2.signOut().then(function () {
      console.log('User signed out.');
    });

	window.location.href = "http://www.ugrad.cs.ubc.ca/~n6o8/landing_page.php";
  }
</script>

<a href="#" onclick="signOut();">Sign out</a>

<form method="GET" action="oracle-test.php">
<input type="submit" value="Reviews" name="getreviews">
<input type="submit" value="Companies" name="getcompanies">
<input type="submit" value="Positions" name="getpositions">
<input type="submit" value="Company Types" name="getcompanytypes">
<input type="submit" value="Departments" name="getdepartments">
<input type="submit" value="Skills" name="getskills">
<input type="submit" value="Skills By Position" name ="getpositionskills">
<input type="submit" value="Locations" name="getlocations">
<input type="submit" value="Company Locations" name="getcompanylocations">
</form>

<p>
  <form method="GET" action="oracle-test.php">
    Companies with ratings:
    <input type="submit" value="1" name="getrating1">
    <input type="submit" value="2" name="getrating2">
    <input type="submit" value="3" name="getrating3">
    <input type="submit" value="4" name="getrating4">
    <input type="submit" value="5" name="getrating5">
  </form>
</p>

<form method="GET" action="oracle-test.php">
  <input type="submit" value="Most Popular Vancouver Companies" name="getvancom">
  <input type="submit" value="Departments With Most Jobs" name="deptjobs">
  <input type="submit" value="Top 5 Desired Skills" name="topskills">
</form>

<?php
//this tells the system that it's no longer just parsing
//html; it's now parsing PHP

$success = True; //keep track of errors so it redirects the page only if there are no errors
$db_conn = OCILogon("ora_n6o8", "a15724032", "ug");

$cookie_name = "user";

if(!is_null($_GET["id"])){
	setcookie($cookie_name, $_GET["id"], time() + (86400 * 30), "/"); // 86400 = 1 day
}

    echo "Cookie name is '" . $cookie_name . "'<br>";
    echo "Value is: " . $_COOKIE[$cookie_name];

    $email =  $_COOKIE[$cookie_name];

if($email){
	while ($row = OCI_Fetch_Array($email_list, OCI_BOTH)) {
		if ($row["EMAIL"] == $email){
			echo "<h1>YOU ARE AN ADMIN!</h1>";
		break;
	}
}
}

echo "<br> The email is " . $email . "<br>";  

echo "<script>";
echo "gapi.load('auth2',function(){gapi.auth2.init();});";
echo "</script>";

function executePlainSQL($cmdstr) { //takes a plain (no bound variables) SQL command and executes it
	//echo "<br>running ".$cmdstr."<br>";
	global $db_conn, $success;
	$statement = OCIParse($db_conn, $cmdstr); //There is a set of comments at the end of the file that describe some of the OCI specific functions and how they work

	if (!$statement) {
		echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
		$e = OCI_Error($db_conn); // For OCIParse errors pass the
		// connection handle
		echo htmlentities($e['message']);
		$success = False;
	}

	$r = OCIExecute($statement, OCI_DEFAULT);
	if (!$r) {
		echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
		$e = oci_error($statement); // For OCIExecute errors pass the statementhandle
		echo htmlentities($e['message']);
		$success = False;
	} else {

	}
	return $statement;

}

function executeBoundSQL($cmdstr, $list) {
	/* Sometimes a same statement will be excuted for severl times, only
	 the value of variables need to be changed.
	 In this case you don't need to create the statement several times;
	 using bind variables can make the statement be shared and just
	 parsed once. This is also very useful in protecting against SQL injection. See example code below for       how this functions is used */

	global $db_conn, $success;
	$statement = OCIParse($db_conn, $cmdstr);

	if (!$statement) {
		echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
		$e = OCI_Error($db_conn);
		echo htmlentities($e['message']);
		$success = False;
	}

	foreach ($list as $tuple) {
		foreach ($tuple as $bind => $val) {
			//echo $val;
			//echo "<br>".$bind."<br>";
			OCIBindByName($statement, $bind, $val);
			unset ($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype

		}
		$r = OCIExecute($statement, OCI_DEFAULT);
		if (!$r) {
			echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
			$e = OCI_Error($statement); // For OCIExecute errors pass the statementhandle
			echo htmlentities($e['message']);
			echo "<br>";
			$success = False;
		}
	}

}

function printAdmins($emails) { //prints results from a select statement
	echo "<br>Admins:<br>";
	echo "<table>";
	echo "<tr><th>EMAIL</th></tr>";

	while ($row = OCI_Fetch_Array($emails, OCI_BOTH)) {
		echo "<tr><td>" . $row["EMAIL"] . "</td></tr>"; //or just use "echo $row[0]"
	}
	echo "</table>";

}

function printReviews($review) { //prints results from a select statement
	echo "<br>Reviews:<br>";
	echo "<table>";
	echo "<tr><th>RID</th><th>Date</th><th>Company</th><th>Position</th><th>Rating</th><th>Comment</th></tr>";

	while ($row = OCI_Fetch_Array($review, OCI_BOTH)) {
		echo "<tr><td>" . $row["RID"] . "</td><td>" . $row["REVIEW_DATE"] . "</td><td>" .
    $row["COMPANYNAME"] . "</td><td>" . $row["POSTITLE"] . "</td><td>" .
    $row["RATING"] . "</td><td>" . $row["REVIEW_COMMENT"] . "</td></tr>"; //or just use "echo $row[0]"
	}
	echo "</table>";

}

function printCompany($company) { //prints results from a select statement
	echo "<br>Companies:<br>";
	echo "<table>";
	echo "<tr><th>Name</th><th>About</th><th>Type</th></tr>";

	while ($row = OCI_Fetch_Array($company, OCI_BOTH)) {
		echo "<tr><td>" . $row["NAME"] . "</td><td>" . $row["ABOUT"] . "</td><td>" . $row["TYPE"] . "</td></tr>"; //or just use "echo $row[0]"
	}
	echo "</table>";

}

function printPosition($position) { //prints results from a select statement
	echo "<br>Positions:<br>";
	echo "<table>";
	echo "<tr><th>Title</th><th>Company</th><th>Duties</th><th>City</th><th>Province</th><th>Type</th></tr>";

	while ($row = OCI_Fetch_Array($position, OCI_BOTH)) {
		echo "<tr><td>" . $row["TITLE"] . "</td><td>" . $row["CNAME"] . "</td><td>" . $row["DUTIES"] . "</td><td>" . $row["CITY"] . "</td><td>" . $row["PROVINCE"] . "</td></tr>"; //or just use "echo $row[0]"
	}
	echo "</table>";

}

function printCompanyType($companytype) { //prints results from a select statement
	echo "<br>Company Type:<br>";
	echo "<table>";
	echo "<tr><th>Type</th><th>Description</th></tr>";

	while ($row = OCI_Fetch_Array($companytype, OCI_BOTH)) {
		echo "<tr><td>" . $row["TYPE"] . "</td><td>" . $row["DESCRIPTION"] . "</td></tr>"; //or just use "echo $row[0]"
	}
	echo "</table>";

}

function printDepartment($department) { //prints results from a select statement
	echo "<br>Departments:<br>";
	echo "<table>";
	echo "<tr><th>Name</th></tr>";

	while ($row = OCI_Fetch_Array($department, OCI_BOTH)) {
		echo "<tr><td>" . $row["NAME"] . "</td></tr>"; //or just use "echo $row[0]"
	}
	echo "</table>";

}

function printSkills($skills) { //prints results from a select statement
	echo "<br>Skills:<br>";
	echo "<table>";
	echo "<tr><th>Name</th><th>Description</th></tr>";

	while ($row = OCI_Fetch_Array($skills, OCI_BOTH)) {
		echo "<tr><td>" . $row["NAME"] . "</td><td>" . $row["DESCRIPTION"] . "</td></tr>"; //or just use "echo $row[0]"
	}
	echo "</table>";

}

function printPosReqSkills($posreqskills) { //prints results from a select statement
	echo "<br>Skills By Position:<br>";
	echo "<table>";
	echo "<tr><th>Position</th><th>Company</th><th>Skill</th></tr>";

	while ($row = OCI_Fetch_Array($posreqskills, OCI_BOTH)) {
		echo "<tr><td>" . $row["PTITLE"] . "</td><td>" . $row["CNAME"] . "</td><td>" . $row["SNAME"] . "</td></tr>"; //or just use "echo $row[0]"
	}
	echo "</table>";

}

function printLocation($location) { //prints results from a select statement
	echo "<br>Locations:<br>";
	echo "<table>";
	echo "<tr><th>City</th><th>Province</th><th>Country</th></tr>";

	while ($row = OCI_Fetch_Array($location, OCI_BOTH)) {
		echo "<tr><td>" . $row["CITY"] . "</td><td>" . $row["PROVINCE"] . "</td><td>" . $row["COUNTRY"] . "</td></tr>"; //or just use "echo $row[0]"
	}
	echo "</table>";

}

function printCompanyLocation($companylocation) { //prints results from a select statement
	echo "<br>Company Locations:<br>";
	echo "<table>";
	echo "<tr><th>Company</th><th>City</th><th>Province</th></tr>";

	while ($row = OCI_Fetch_Array($companylocation, OCI_BOTH)) {
		echo "<tr><td>" . $row["CNAME"] . "</td><td>" . $row["CITY"] . "</td><td>" . $row["PROVINCE"] . "</td></tr>"; //or just use "echo $row[0]"
	}
	echo "</table>";

}

function printTopSkills($topskills) { //prints results from a select statement
	echo "<br>Most Desired Skills:<br>";
	echo "<table>";
	echo "<tr><th>Skill</th><th>Number of positions that require this skill</th></tr>";

	while ($row = OCI_Fetch_Array($topskills, OCI_BOTH)) {
		echo "<tr><td>" . $row["NAME"] . "</td><td>" . $row["NUM"] . "</td></tr>"; //or just use "echo $row[0]"
	}
	echo "</table>";

}

function printTopDepartment($topdept) { //prints results from a select statement
	echo "<br>Departments with most jobs:<br>";
	echo "<table>";
	echo "<tr><th>Department</th><th>Number of Positions</th></tr>";

	while ($row = OCI_Fetch_Array($topdept, OCI_BOTH)) {
		echo "<tr><td>" . $row["DNAME"] . "</td><td>" . $row["NUM"] . "</td></tr>"; //or just use "echo $row[0]"
	}
	echo "</table>";

}

// Connect Oracle...
if ($db_conn) {

	if (array_key_exists('reset', $_POST)) {
		// Drop old table...
		echo "<br> dropping table <br>";
		executePlainSQL("Drop table tab1");

		// Create new table...
		echo "<br> creating new table <br>";
		executePlainSQL("create table tab1 (nid number, name varchar2(30), primary key (nid))");
		OCICommit($db_conn);

	} else
		if (array_key_exists('insertsubmit', $_POST)) {
			//Getting the values from user and insert data into the table
			$tuple = array (
				":bind1" => $_POST['insNo'],
				":bind2" => $_POST['insName']
			);
			$alltuples = array (
				$tuple
			);
			executeBoundSQL("insert into tab1 values (:bind1, :bind2)", $alltuples);
			OCICommit($db_conn);

		} else
			if (array_key_exists('updatesubmit', $_POST)) {
				// Update tuple using data from user
				$tuple = array (
					":bind1" => $_POST['oldName'],
					":bind2" => $_POST['newName']
				);
				$alltuples = array (
					$tuple
				);
				executeBoundSQL("update tab1 set name=:bind2 where name=:bind1", $alltuples);
				OCICommit($db_conn);

			} else
				if (array_key_exists('dostuff', $_POST)) {
					// Insert data into table...
					executePlainSQL("insert into tab1 values (10, 'Frank')");
					// Inserting data into table using bound variables
					$list1 = array (
						":bind1" => 6,
						":bind2" => "All"
					);
					$list2 = array (
						":bind1" => 7,
						":bind2" => "John"
					);
					$allrows = array (
						$list1,
						$list2
					);
					executeBoundSQL("insert into tab1 values (:bind1, :bind2)", $allrows); //the function takes a list of lists
					// Update data...
					//executePlainSQL("update tab1 set nid=10 where nid=2");
					// Delete data...
					//executePlainSQL("delete from tab1 where nid=1");
					OCICommit($db_conn);
				} else
          if (array_key_exists('getreviews', $_GET)){
            $review = executePlainSQL("select * from review");
            printReviews($review);
            OCICommit($db_conn);
          } else
          if (array_key_exists('getcompanies', $_GET)){
            $company = executePlainSQL("select * from coopcompany");
            printCompany($company);
            OCICommit($db_conn);
          } else
          if (array_key_exists('getpositions', $_GET)){
            $position = executePlainSQL("select * from positionforcompany");
            printPosition($position);
            OCICommit($db_conn);
          } else
          if (array_key_exists('getcompanytypes', $_GET)){
            $companytype = executePlainSQL("select * from companytype");
            printCompanyType($companytype);
            OCICommit($db_conn);
          } else
          if (array_key_exists('getdepartments', $_GET)){
            $department = executePlainSQL("select * from department");
            printDepartment($department);
            OCICommit($db_conn);
          } else
          if (array_key_exists('getskills', $_GET)){
            $skills = executePlainSQL("select * from skills");
            printSkills($skills);
            OCICommit($db_conn);
          } else
          if (array_key_exists('getpositionskills', $_GET)){
            $posreqskills = executePlainSQL("select * from positionrequiresskill");
            printPosReqSkills($posreqskills);
            OCICommit($db_conn);
          } else
          if (array_key_exists('getlocations', $_GET)){
            $location = executePlainSQL("select * from location");
            printLocation($location);
            OCICommit($db_conn);
          } else
          if (array_key_exists('getcompanylocations', $_GET)){
            $companylocation = executePlainSQL("select * from companylocation");
            printCompanyLocation($companylocation);
            OCICommit($db_conn);
          } else
          if (array_key_exists('getrating1', $_GET)){
            $company = executePlainSQL("select distinct name, about, type from coopcompany cc, review r where cc.name=r.companyname and r.rating=1");
            printCompany($company);
            OCICommit($db_conn);
          } else
          if (array_key_exists('getrating2', $_GET)){
            $company = executePlainSQL("select distinct name, about, type from coopcompany cc, review r where cc.name=r.companyname and r.rating=2");
            printCompany($company);
            OCICommit($db_conn);
          } else
          if (array_key_exists('getrating3', $_GET)){
            $company = executePlainSQL("select distinct name, about, type from coopcompany cc, review r where cc.name=r.companyname and r.rating=3");
            printCompany($company);
            OCICommit($db_conn);
          } else
          if (array_key_exists('getrating4', $_GET)){
            $company = executePlainSQL("select distinct name, about, type from coopcompany cc, review r where cc.name=r.companyname and r.rating=4");
            printCompany($company);
            OCICommit($db_conn);
          } else
          if (array_key_exists('getrating5', $_GET)){
            $company = executePlainSQL("select distinct name, about, type from coopcompany cc, review r where cc.name=r.companyname and r.rating=5");
            printCompany($company);
            OCICommit($db_conn);
          } else
          if (array_key_exists('getvancom', $_GET)){
            $company = executePlainSQL("select distinct name, about, type from coopcompany cc, companylocation cl, review r where cc.name=cl.cname and cc.name=r.companyname and cl.city='Vancouver' and r.rating>=4");
            printCompany($company);
            OCICommit($db_conn);
          } else
          if (array_key_exists('deptjobs', $_GET)){
            executePlainSql("drop view temp");
            executePlainSql("create view Temp(cname, poscount) as (select cname, COUNT(*) as poscount from PositionForCompany GROUP BY cname)");
            $topdept = executePlainSQL("select dname, max(posc) as num from (select dname, sum(poscount) as posc from companyhiresfordept c, temp t where t.cname=c.cname group by dname) where rownum<=1 group by dname");


            printTopDepartment($topdept);
            OCICommit($db_conn);
          } else
          if (array_key_exists('topskills', $_GET)){
            $topskills = executePlainSQL("select name, num
                                            from (select sname as name, count(*) as num
                                                  from positionrequiresskill
                                                  group by sname
                                                  order by count(*) desc)
                                            where rownum<=5");
            printTopSkills($topskills);
            OCICommit($db_conn);
          }

	if ($_POST && $success) {
		//POST-REDIRECT-GET -- See http://en.wikipedia.org/wiki/Post/Redirect/Get
		header("location: oracle-test.php");
	} else {
		// Select data...
		// $review = executePlainSQL("select * from review");
    // $company = executePlainSQL("select * from coopcompany");
    // $position = executePlainSQL("select * from positionforcompany");
    // $companytype = executePlainSQL("select * from companytype");
    // $department = executePlainSQL("select * from department");
    // $skills = executePlainSQL("select * from skills");
    // $location = executePlainSQL("select * from location");
		// printReviews($review);
    // printCompany($company);
    // printPosition($position);
    // printCompanyType($companytype);
    // printDepartment($department);
    // printSkills($skills);
    // printLocation($location);
	}

	//Commit to save changes...
	OCILogoff($db_conn);
} else {
	echo "cannot connect";
	$e = OCI_Error(); // For OCILogon errors pass no handle
	echo htmlentities($e['message']);
}



/* OCILogon() allows you to log onto the Oracle database
     The three arguments are the username, password, and database
     You will need to replace "username" and "password" for this to
     to work.
     all strings that start with "$" are variables; they are created
     implicitly by appearing on the left hand side of an assignment
     statement */

/* OCIParse() Prepares Oracle statement for execution
      The two arguments are the connection and SQL query. */
/* OCIExecute() executes a previously parsed statement
      The two arguments are the statement which is a valid OCI
      statement identifier, and the mode.
      default mode is OCI_COMMIT_ON_SUCCESS. Statement is
      automatically committed after OCIExecute() call when using this
      mode.
      Here we use OCI_DEFAULT. Statement is not committed
      automatically when using this mode */

/* OCI_Fetch_Array() Returns the next row from the result data as an
     associative or numeric array, or both.
     The two arguments are a valid OCI statement identifier, and an
     optinal second parameter which can be any combination of the
     following constants:

     OCI_BOTH - return an array with both associative and numeric
     indices (the same as OCI_ASSOC + OCI_NUM). This is the default
     behavior.
     OCI_ASSOC - return an associative array (as OCI_Fetch_Assoc()
     works).
     OCI_NUM - return a numeric array, (as OCI_Fetch_Row() works).
     OCI_RETURN_NULLS - create empty elements for the NULL fields.
     OCI_RETURN_LOBS - return the value of a LOB of the descriptor.
     Default mode is OCI_BOTH.  */
?>
</html>