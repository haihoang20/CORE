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
<?php
require 'functions.php';
include 'header.php';
$success = True; //keep track of errors so it redirects the page only if there are no errors
$db_conn = OCILogon("ora_c9f9", "a44262095", "ug");

$Error = "";
		if (array_key_exists('submit_review', $_POST)) {
                        
                                if ('---' == $_POST['companyname']) {
                                        $Error = "Please select a company name.";
                                        $success = false;
                                }
        

                                else if (empty($_POST['review_comment'])) {
                                        $Error = "Review body cannot be empty.";
                                        $success = false;
                                }

}

		else if (array_key_exists('add_company', $_POST)) {
                
                                if (empty($_POST['companyname']) || empty($_POST['companyabout']) ) {
                                        $Error = "Company name cannot be empty";
                                } 

                }

echo '<div class="error">' . $Error . '</div>';
?>
<h2>User Profile</h2>


<div class="review_form form">
        <h3>Write a new Review:</h3>
        <form method="POST" action="user-profile.php">


        <p>Company Name</p>
        <?php
        if ($db_conn) {

                $companynames = executePlainSQL("select name from coopcompany");
                printCompanyNames($companynames);

        echo "<p>Position Title</p>";
                $postitles = executePlainSQL("select title from positionforcompany");
                printPosTitles($postitles);
        }
        ?>

        <p>Rating (between 1 and 5)</p>
        <input type="text" name="rating" />

        <p>Review Body</p>
        <p><textarea name="review_comment"></textarea></p>
        <input type="submit" value="Submit Review" name="submit_review"></p>
        </form>
</div>

<div class="add_company_form form">
        <h3>Add a Company</h3>
        <form method="POST" action="user-profile.php">

        <p>Company Name</p>
        <p><input type="text" name="companyname"></p>
        <?php
        if ($db_conn) {
        
        echo "<p>Company Type</p>";
                $types = executePlainSQL("select type from companytype");
                echo "<p>";
                printTypes($types);
                echo "</p>";
        }
        ?>

        <p>About Company</p>
        <p><textarea name="companyabout"></textarea></p>
        <input type="submit" value="Add Company" name="add_company"></p>
        </form>
</div>
<div class="clear-both"></div>




<?php

date_default_timezone_set('America/Los_Angeles');


// Connect Oracle...
if ($db_conn) {
		if (array_key_exists('submit_review', $_POST)) {
			//Getting the values from user and insert data into the table
                                
                                $postitle = $_POST['postitle'];
                                if ('---' == $postitle) {
                                        $postitle = null;
                                }
			             $tuple = array (
                                        ":bind1" => $_POST['review_comment'],
                                        ":bind2" =>date("M d Y, g:ia "),
                                        ":bind3" => $_POST['companyname'],
                                        ":bind4" => 101, // dummy value, coop student id
                                        ":bind5" => $postitle,
                                        ":bind6" => $_POST['rating']
                                        );
                                        $alltuples = array (
                                                $tuple
                                        );
                                        echo($tuple);
                                        executeBoundSQL("insert into review values ((select r1.rid from review r1 where not exists (select r2.rid from review r2 where r2.rid > r1.rid)) + 1, :bind1, :bind2, :bind3, :bind4,
                                                        :bind5, :bind6)", $alltuples);
                                        OCICommit($db_conn);

		} else
			if (array_key_exists('add_company', $_POST)) {

			             $tuple = array (
                                        ":bind1" => $_POST['companyname'],
                                        ":bind2" => $_POST['companyabout'],
                                        ":bind3" => $_POST['companytype']
                                        );
                                        $alltuples = array (
                                                $tuple
                                        );

                                        executeBoundSQL("insert into coopcompany values (:bind1, :bind2, :bind3)", $alltuples); 

                                        OCICommit($db_conn);
			}

	if ($_POST && $success) {
		//POST-REDIRECT-GET -- See http://en.wikipedia.org/wiki/Post/Redirect/Get
		header("location: user-profile.php");
	} else {
		// Select data...
		$review = executePlainSQL("select * from review");
                $company = executePlainSQL("select * from coopcompany");
                //$position = executePlainSQL("select * from positionforcompany");
                //$department = executePlainSQL("select * from department");
                //$skills = executePlainSQL("select * from skills");
                //$location = executePlainSQL("select * from location");
                //$companytype = executePlainSQL("select * from companytype");
                printReviews($review);
                //printCompanyType($companytype);
                printCompany($company);
                //printPosition($position);
                //printDepartment($department);
                //printSkills($skills);
                //printLocation($location);
                
                //$result = executePlainSQL("select * from tab1");
                //printResult($result);

	}

	//Commit to save changes...
	OCILogoff($db_conn);
} else {
	echo "cannot connect";
	$e = OCI_Error(); // For OCILogon errors pass no handle
	echo htmlentities($e['message']);
  }
                
?>
