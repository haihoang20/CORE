<?php
require 'functions.php';
include 'header.php';
$success = True; //keep track of errors so it redirects the page only if there are no errors
$db_conn = OCILogon($core_oracle_user, $core_oracle_password, "ug");


/****** Error Checking ******/
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

                                // make sure company name and position's company name match.
                                if ('---' != $_POST['postitle']) {
                                        $comp = array ( array ( ":bind1" => $_POST['postitle']));
                                        $query = executeBoundSQL("select cname from positionforcompany where title=:bind1", $comp);
                                        $row = OCI_Fetch_Array($query, OCI_BOTH);
                                        if ($row['CNAME'] != $_POST['companyname']) {
                                                $success=false;
                                                $Error = "The chosen position is not at the company you have selected.";        
                                        }
                                }
                }

		else if (array_key_exists('add_company', $_POST)) {
                
                                if (empty($_POST['companyname'])) {
                                        $Error = "Company name cannot be empty.";
                                        $success = false;
                                } 
                                else if (empty($_POST['companyabout'])) {
                                        $Error = "'About Company' cannot be empty.";
                                        $success = false;
                                }
                                if ('---' == $_POST['company_type']) {
                                        $Error = "Please select a company type.";
                                        $success = false;
                                }

                }
		else if (array_key_exists('add_skill', $_POST)) {
        
                                if (empty($_POST['skillname'])) {
                                        $Error = "Skill name cannot be empty.";
                                        $success = false;
                                } 
                                if (empty($_POST['skilldescription'])) {
                                        $Error = "Skill description cannot be empty.";
                                        $success = false;
                                } 
                                

                }
		else if (array_key_exists('add_position', $_POST)) {
        
                                if (empty($_POST['positiontitle'])) {
                                        $Error = "Position title cannot be empty.";
                                        $success = false;
                                } 
                                else if (empty($_POST['companyname'])) {
                                        $Error = "Company name cannot be empty.";
                                        $success = false;
                                }
                                //if (!empty($_POST['skill'])) {
                                //        echo "SKILLS:";
                                //        echo $_POST['skill'];
                                //        foreach ($_POST['skill'] as $skill) {
                                //                echo "skill:" . $skill; 
                                //        }
                                //        //print_r($_POST['skill']);
                                //      $success = false;
                                //}

                }

if ($db_conn) {
                if ($success) {
                        if (array_key_exists('submit_review', $_POST)) {
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
                                                ":bind3" => $_POST['company_type']
                                             );
                                             $alltuples = array (
                                                     $tuple
                                             );

                                             executeBoundSQL("insert into coopcompany values (:bind1, :bind2, :bind3)", $alltuples); 

                                             OCICommit($db_conn);
                        } else if (array_key_exists('add_skill', $_POST)) {

                                             $tuple = array (
                                                ":bind1" => $_POST['skillname'],
                                                ":bind2" => $_POST['skilldescription'],
                                             );
                                             $alltuples = array (
                                                     $tuple
                                             );

                                             executeBoundSQL("insert into skill values (:bind1, :bind2)", $alltuples); 

                                             OCICommit($db_conn);
                        }
                        
                        else if (array_key_exists('add_position', $_POST)) {
                                             $tuple = array (
                                                ":bind1" => $_POST['positiontitle'],
                                                ":bind2" => $_POST['companyname'],
                                                ":bind3" => $_POST['positionduties']
                                             );
                                             $alltuples = array (
                                                     $tuple
                                             );
                                             executeBoundSQL("insert into positionforcompany values (:bind1, :bind2, :bind3)", $alltuples);
                                             
                                             foreach ($_POST['skill'] as $skill) {
                                                $s = array ( 
                                                        array ( 
                                                                ":bind1" => $_POST['positiontitle'], 
                                                                ":bind2" => $_POST['companyname'],
                                                                ":bind3" => $skill 
                                                         ));
                                                executeBoundSQL("insert into positionrequiresskill values(:bind1, :bind2, :bind3)", $s);
                                             }

                                             OCICommit($db_conn);
                        }
        }

	if ($_POST && $success) {
                header("location: user-profile.php");
                exit;
	} else {

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
                $postitles = executePlainSQL("select * from positionforcompany");
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
                printTypeNames($types);
                echo "</p>";
        }
        ?>

        <p>About Company</p>
        <p><textarea name="companyabout"></textarea></p>
        <input type="submit" value="Add Company" name="add_company"></p>
        </form>
</div>

<div class="add_skill_form form">
        <h3>Add a Skill</h3>
        <form method="POST" action="user-profile.php">

        <p>Skill Name</p>
        <p><input type="text" name="skillname"></p>
        <p>Description</p>
        <p><textarea name="skilldescription"></textarea></p>
        <input type="submit" value="Add Skill" name="add_skill"></p>
        </form>
</div>

<div class="add_position_form form">
        <h3>Add a Position</h3>
        <form method="POST" action="user-profile.php">

        <p>Position Title</p>
        <p><input type="text" name="positiontitle"></p>
        <p>Position Duties</p>
        <p><textarea name="positionduties"></textarea></p>

        
        <?php
        if ($db_conn) {

                echo "<p>Company</p>";
                $companynames = executePlainSQL("select name from coopcompany");
                printCompanyNames($companynames);

                echo "<p>Required Skills</p>";
                $skills = executePlainSQL("select name from skill");
                printSkillNames($skills);
        } ?>

        <div></div>
        <input type="submit" value="Add Position" name="add_position"></p>
        </form>
</div>

<div class="clear-both"></div>

<?php
		$review = executePlainSQL("select * from review");
                $company = executePlainSQL("select * from coopcompany");
                //$department = executePlainSQL("select * from department");
                $skills = executePlainSQL("select * from skill");
                //$location = executePlainSQL("select * from location");
                //$companytype = executePlainSQL("select * from companytype");
                printReviews($review);
                //printCompanyType($companytype);
                printCompany($company);
                
                
                //printDepartment($department);
                printSkills($skills);
                //printLocation($location);
                
                //$result = executePlainSQL("select * from tab1");
                //printResult($result);
                
                 echo "<br>Positions:<br>";
                echo "<table>";
                echo "<tr><th>Title</th><th>Company</th><th>Duties</th><th>required skills</th></tr>";
                $positions = executePlainSQL("select * from positionforcompany");
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

	//Commit to save changes...
	OCILogoff($db_conn);
} else {
	echo "cannot connect";
	$e = OCI_Error(); // For OCILogon errors pass no handle
	echo htmlentities($e['message']);
  }
                
?>
