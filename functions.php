<?php
function executePlainSQL($cmdstr) { 
	global $db_conn, $success;
	$statement = OCIParse($db_conn, $cmdstr); 
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
        return $statement;
} 

/*** functions to display table info ***/
function printCompanyType($companytype) { 
	echo "<br>Company Type:<br>";
	echo "<table>";
	echo "<tr><th>Type</th><th>Description</th></tr>";

	while ($row = OCI_Fetch_Array($companytype, OCI_BOTH)) {
		echo "<tr><td>" . $row["TYPE"] . "</td><td>" . $row["DESCRIPTION"] . "</td></tr>"; //or just use "echo $row[0]"
	}
	echo "</table>";

}

function printReviews($review) { 
	echo "<br>Reviews:<br>";
	echo "<table>";
	echo "<tr><th>RID</th><th>Date</th><th>Company</th><th>Position</th><th>Rating</th><th>Comment</th></tr>";

	while ($row = OCI_Fetch_Array($review, OCI_BOTH)) {
		echo "<tr><td>" . $row["RID"] . "</td><td>" . $row[REVIEW_DATE] . "</td><td>" .
    $row["COMPANYNAME"] . "</td><td>" . $row["POSTITLE"] . "</td><td>" .
    $row["RATING"] . "</td><td>" . $row["REVIEW_COMMENT"] . "</td><td><a href='edit-review.php?rid=" . $row["RID"] . "'>Edit Review</a></td></tr>"; //or just use "echo $row[0]"
	}
	echo "</table>";

}

function printCompany($company) { //prints results from a select statement
	echo "<br>Companies:<br>";
	echo "<table>";
	echo "<tr><th>Name</th><th>About</th><th>Type</th></tr>";

	while ($row = OCI_Fetch_Array($company, OCI_BOTH)) {
		echo "<tr><td>" . $row["NAME"] . "</td><td>" . $row["ABOUT"] . "</td><td>" . $row["TYPE"] . "</td> <td><a href='edit-company.php?name=" . $row["NAME"] . "'>Edit Company</a></td> </tr>";
	}
	echo "</table>";

}

function printPositionWithoutSkills($position) { 
		echo "<td>" . $position["TITLE"] . "</td><td>" . $position["CNAME"] . "</td><td>" . $position["DUTIES"] . "</td><td>";
                printSkillsForPosition($position['TITLE'], $position['CNAME']);
}

function printSkillsForPosition($skills) {
        
	while ($skill = OCI_Fetch_Array($skills, OCI_BOTH)) {
		echo $skill["SNAME"] . "  ";
        }
        
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

function printLocation($location) { //prints results from a select statement
	echo "<br>Locations:<br>";
	echo "<table>";
	echo "<tr><th>City</th><th>Province</th><th>Country</th></tr>";

	while ($row = OCI_Fetch_Array($location, OCI_BOTH)) {
		echo "<tr><td>" . $row["CITY"] . "</td><td>" . $row["PROVINCE"] . "</td><td>" . $row["COUNTRY"] . "</td></tr>"; //or just use "echo $row[0]"
	}
	echo "</table>";

}

/******* Functions for generating form fields *******/

function printCompanyNames($companies, $selected) { // if selected, we're editing
        echo "<select name='companyname'>";
        
        if (!isset($selected)) {
         echo "<option selected value='---'>---</option>";
        }
        else {
         echo "<option value='---'>---</option>";
        }
	while ($row = OCI_Fetch_Array($companies, OCI_BOTH)) {
                if (isset($selected) && $selected == $row['NAME']) {
                        echo "<option selected value='" . $row['NAME'] . "'>" . $row['NAME'] . "</option>";
                }
                else {echo "<option value='" . $row['NAME'] . "'>" . $row['NAME'] . "</option>";}
	}
        echo "</select>";
}

function printTypeNames($types, $selected) { 
        echo "<select name='company_type'>";
        
        if (!isset($selected)) {
         echo "<option selected value='---'>---</option>";
        }
        else {
         echo "<option value='---'>---</option>";
        }
	while ($row = OCI_Fetch_Array($types, OCI_BOTH)) {
                if (isset($selected) && $selected == $row['TYPE']) {
                        echo "<option selected value='" . $row['TYPE'] . "'>" . $row['TYPE'] . "</option>";
                }
                else {echo "<option value='" . $row['TYPE'] . "'>" . $row['TYPE'] . "</option>";}
	}
        echo "</select>";
}


function printPosTitles($positions, $selected) { // if selected, we're editing
        echo "<select name='postitle'>";
        if (!isset($selected)) {
         echo "<option selected value='---'>---</option>";
        }
        else {
         echo "<option value='---'>---</option>";
        }
	while ($row = OCI_Fetch_Array($positions, OCI_BOTH)) {
                
                if (isset($selected) && $selected == $row['TITLE']) {
                        echo "<option selected value='" . $row['TITLE'] . "'>" . $row['TITLE'] . "</option>";
                }
                else {echo "<option value='" . $row['TITLE'] . "'>" . $row['TITLE'] . "</option>";}
	}
        echo "</select>";
}

function printLocationNames($locations, $selected) { // if selected, we're editing
        echo "<select name='location'>";
        if (!isset($selected)) {
         echo "<option selected value='---'>---</option>";
        }
        else {
         echo "<option value='---'>---</option>";
        }
	while ($row = OCI_Fetch_Array($locations, OCI_BOTH)) {
                
                //if (isset($selected) && $selected == $row['TITLE']) {
                //        echo "<option selected value='" . $row['TITLE'] . "'>" . $row['TITLE'] . "</option>";
                //}
                echo "<option value='" . $row['CITY'] . "-" . $row['PROVINCE'] . "'>" . $row['CITY'] . " - " . $row['PROVINCE'] . "</option>";
	}
        echo "</select>";
}

function printSkillNames($skills) { 
        echo "<select multiple name='skill[]'>";
        //if (!isset($selected)) {
        // echo "<option selected value='---'>---</option>";
        //}
        //else {
        // echo "<option value='---'>---</option>";
        //}
	while ($row = OCI_Fetch_Array($skills, OCI_BOTH)) {
                
                //if (isset($selected) && $selected == $row['TITLE']) {
                //        echo "<option selected value='" . $row['TITLE'] . "'>" . $row['TITLE'] . "</option>";
                //}
                echo "<option value='" . $row['NAME'] . "'>" . $row['NAME'] . "</option>";
	}
        echo "</select>";
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
