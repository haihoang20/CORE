<!-- Key functions for User-Profile Page -->

<?php

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
		echo "<tr><td>" . $row["RID"] . "</td><td>" . $row["REVIEW_DATE"] . "</td><td>" .
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

?>
