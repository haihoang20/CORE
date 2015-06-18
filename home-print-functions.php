<!-- CORE HOME PAGE - PRINT FUNCTIONS (Mainly used for static queries) -->

<?php

////// FOR ALL ATTRIBUTE PRINTS //////

// Print Review tuples with all attributes
function printReviews($review) { //prints results from a select statement
	echo "<div class='results'><h3>Reviews:</h3>";
	echo "<table >";
	echo "<tr><th>RID</th><th>Date</th><th>Company</th><th>Position</th><th>Rating</th><th>Comment</th></tr>";

	while ($row = OCI_Fetch_Array($review, OCI_BOTH)) {
		echo "<tr><td>" . $row["RID"] . "</td><td>" . $row["REVIEW_DATE"] . "</td><td>" .
    $row["COMPANYNAME"] . "</td><td>" . $row["POSTITLE"] . "</td><td>" .
    $row["RATING"] . "</td><td>" . $row["REVIEW_COMMENT"] . "</td></tr>"; //or just use "echo $row[0]"
	}
	echo "</table></div>";

}

// Print Company tuples with all attributes
function printCompany($companies) { //prints results from a select statement
	echo "<h3>Companies:</h3>";
	echo "<table>";
	echo "<tr><th>Name</th><th>About</th><th>Type</th><th>Hires From Department</th></tr>";

	while ($company = OCI_Fetch_Array($companies, OCI_BOTH)) {
					echo "<tr>";
					printCompanyWithoutHiresFrom($company);
					$s = array ( array (
									":bind1" => $company["NAME"]
									));
					$HiresFrom = executeBoundSQL("select d.name from department d, companyhiresfordept ch where d.name = ch.dname and ch.cname=:bind1", $s);
					echo "<td>";
					printHiresFromForCompany($HiresFrom);
					echo "</td>";
					echo "</tr>";
	}
	echo "</table>";

}

function printCompanyWithoutHiresFrom($company) {
        echo "<td>" . $company["NAME"] . "</td><td>" . $company["ABOUT"] . "</td><td>" . $company["TYPE"] . "</td>";
}
function printHiresFromForCompany($HiresFrom) {
	while ($dept = OCI_Fetch_Array($HiresFrom, OCI_BOTH)) {
		echo $dept["NAME"] . "  ";
        }
}

// Print Position tuples with all attributes
function printPosition($positions) { //prints results from a select statement
	echo "<h3>Positions:</h3>";
	echo "<table>";
	echo "<tr><th>Title</th><th>Company</th><th>Duties</th><th>Required Skills</th></tr>";
	while ($position = OCI_Fetch_Array($positions, OCI_BOTH)) {
					echo "<tr>";
					printPositionWithoutSkills($position);
					$s = array ( array (
									":bind1" => $position["TITLE"],
									":bind2" => $position["CNAME"]));
					$skills = executeBoundSQL("select sname from positionrequiresskill where ptitle=:bind1 and cname=:bind2", $s);
					echo "<td>";
					printSkillsForPosition($skills);
					echo "</td>";
					echo "</tr>";
	}
	echo "</table>";
}

function printPositionWithoutSkills($position) {
		echo "<td>" . $position["TITLE"] . "</td><td>" . $position["CNAME"] . "</td><td>" . $position["DUTIES"] . "</td>";
}

function printSkillsForPosition($skills) {

	while ($skill = OCI_Fetch_Array($skills, OCI_BOTH)) {
		echo $skill["SNAME"] . " | ";
        }

}

// Print CompanyType tuples with all attributes
function printCompanyType($companytype) { //prints results from a select statement
	echo "<div class='results'><h3>Company Type:</h3>";
	echo "<table >";
	echo "<tr><th>Type</th><th>Description</th></tr>";

	while ($row = OCI_Fetch_Array($companytype, OCI_BOTH)) {
		echo "<tr><td>" . $row["TYPE"] . "</td><td>" . $row["DESCRIPTION"] . "</td></tr>"; //or just use "echo $row[0]"
	}
	echo "</table></div>";

}

// Print Department tuples with all attributes
function printDepartment($department) { //prints results from a select statement
	echo "<div class='results'><h3>Departments:</h3>";
	echo "<table >";
	echo "<tr><th>Name</th></tr>";

	while ($row = OCI_Fetch_Array($department, OCI_BOTH)) {
		echo "<tr><td>" . $row["NAME"] . "</td></tr>"; //or just use "echo $row[0]"
	}
	echo "</table></div>";

}

// Print Skills tuples with all attributes
function printSkills($skills) { //prints results from a select statement
	echo "<div class='results'><h3>Skills:</h3>";
	echo "<table >";
	echo "<tr><th>Name</th><th>Description</th></tr>";

	while ($row = OCI_Fetch_Array($skills, OCI_BOTH)) {
		echo "<tr><td>" . $row["NAME"] . "</td><td>" . $row["DESCRIPTION"] . "</td></tr>"; //or just use "echo $row[0]"
	}
	echo "</table></div>";

}

// Print Location tuples with all attributes
function printLocation($location) { //prints results from a select statement
	echo "<div class='results'><h3>Locations:</h3>";
	echo "<table >";
	echo "<tr><th>City</th><th>Province</th><th>Country</th></tr>";

	while ($row = OCI_Fetch_Array($location, OCI_BOTH)) {
		echo "<tr><td>" . $row["CITY"] . "</td><td>" . $row["PROVINCE"] . "</td><td>" . $row["COUNTRY"] . "</td></tr>"; //or just use "echo $row[0]"
	}
	echo "</table></div>";

}

// Print CompanyLocation tuples with all attributes
function printCompanyLocation($companylocation) { //prints results from a select statement
	echo "<div class='results'><h3>Company Locations:</h3>";
	echo "<table >";
	echo "<tr><th>Company</th><th>City</th><th>Province</th></tr>";

	while ($row = OCI_Fetch_Array($companylocation, OCI_BOTH)) {
		echo "<tr><td>" . $row["CNAME"] . "</td><td>" . $row["CITY"] . "</td><td>" . $row["PROVINCE"] . "</td></tr>"; //or just use "echo $row[0]"
	}
	echo "</table></div>";

}


////// INTERESTING QUERIES PRINTS ///////

// Print the Top Skills desired for all jobs in the database,
// with the name of the skill and the count of jobs listed as requiring it
function printTopSkills($topskills) { //prints results from a select statement
	echo "<div class='results'><h3>Most Desired Skills:</h3>";
	echo "<table >";
	echo "<tr><th>Skill</th><th>Number of positions that require this skill</th></tr>";

	while ($row = OCI_Fetch_Array($topskills, OCI_BOTH)) {
		echo "<tr><td>" . $row["NAME"] . "</td><td>" . $row["NUM"] . "</td></tr>"; //or just use "echo $row[0]"
	}
	echo "</table></div>";

}

// Print the Top Departments hired from, with Department Name and the Number of Jobs
function printTopDepartment($topdept) { //prints results from a select statement
	echo "<div class='results'><h3>Departments with most jobs:</h3>";
	echo "<table >";
	echo "<tr><th>Department</th><th>Number of Positions</th></tr>";

	while ($row = OCI_Fetch_Array($topdept, OCI_BOTH)) {
		echo "<tr><td>" . $row["DNAME"] . "</td><td>" . $row["NUM"] . "</td></tr>"; //or just use "echo $row[0]"
	}
	echo "</table></div>";

}

?>
