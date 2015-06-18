<!-- Key functions for User-Profile Page -->

<?php

/*** functions for display table info ***/

function printReviews($review) {
	echo "<h3>Reviews:</h3>";
	echo "<table>";
	echo "<tr><th>RID</th><th>Date</th><th>Company</th><th>Position</th><th>Rating</th><th>Comment</th></tr>";

	while ($row = OCI_Fetch_Array($review, OCI_BOTH)) {
		echo "<tr><td>" . $row["RID"] . "</td><td>" . $row["REVIEW_DATE"] . "</td><td>" .
    $row["COMPANYNAME"] . "</td><td>" . $row["POSTITLE"] . "</td><td>" .
    $row["RATING"] . "</td><td>" . $row["REVIEW_COMMENT"] . "</td><td><a href='edit-review.php?rid=" . $row["RID"] . "'>Edit Review</a></td></tr>"; //or just use "echo $row[0]"
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

function printPositionWithoutSkills($position) {
		echo "<td>" . $position["TITLE"] . "</td><td>" . $position["CNAME"] . "</td><td>" . $position["DUTIES"] . "</td>";
}

function printSkillsForPosition($skills) {

	while ($skill = OCI_Fetch_Array($skills, OCI_BOTH)) {
		echo $skill["SNAME"] . " | ";
        }

}

function printSkills($skills) { //prints results from a select statement
	echo "<h3>Skills:</h3>";
	echo "<table>";
	echo "<tr><th>Name</th><th>Description</th></tr>";

	while ($row = OCI_Fetch_Array($skills, OCI_BOTH)) {
		echo "<tr><td>" . $row["NAME"] . "</td><td>" . $row["DESCRIPTION"] . "</td></tr>"; //or just use "echo $row[0]"
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
                        echo "<option selected value='" . $row['TITLE'] . "'>" . $row['TITLE'] . " - " . $row['CNAME'] . "</option>";
                }
                else {echo "<option value='" . $row['TITLE'] . "'>" . $row['TITLE'] .  " - " . $row['CNAME'] . "</option>";}
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

                echo "<option value='" . $row['CITY'] . "-" . $row['PROVINCE'] . "'>" . $row['CITY'] . " - " . $row['PROVINCE'] . "</option>";
	}
        echo "</select>";
}

function printSkillNames($skills, $current) {
        echo "<select multiple name='skill[]'>";
	while ($row = OCI_Fetch_Array($skills, OCI_BOTH)) {

                if (in_array($row["NAME"], $current)) {
                echo "<option selected value='" . $row['NAME'] . "'>" . $row['NAME'] . "</option>";
                }
                else {
                        echo "<option value='" . $row['NAME'] . "'>" . $row['NAME'] . "</option>";
                }
	}
        echo "</select>";
}

/* function for printing select box when editing company to select what departments they hire for */
function printDepartmentNamesMulti($departments, $current) {
        echo "<select multiple name='department[]'>";
	while ($row = OCI_Fetch_Array($departments, OCI_BOTH)) {

                if (in_array($row["NAME"], $current)) {
                echo "<option selected value='" . $row['NAME'] . "'>" . $row['NAME'] . "</option>";
                }
                else {
                        echo "<option value='" . $row['NAME'] . "'>" . $row['NAME'] . "</option>";
                }
	}
        echo "</select>";
}

?>
