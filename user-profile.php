
<?php
require 'functions.php';
include 'header.php';
$success = True; //keep track of errors so it redirects the page only if there are no errors
$db_conn = OCILogon("ora_c9f9", "a44262095", "ug");


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

                }
		else if (array_key_exists('add_skill', $_POST)) {
        
                                if (empty($_POST['skillname'])) {
                                        $Error = "Skill name cannot be empty.";
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
                }

date_default_timezone_set('America/Los_Angeles');

if ($db_conn) {
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
                                        ":bind3" => $_POST['companytype']
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
                                     $break = strrpos($_POST['location'], '-');
                                     $city = substr($_POST['location'], 0, $break);
                                     $province = substr($_POST['location'], $break + 1);
			             $tuple = array (
                                        ":bind1" => $_POST['positiontitle'],
                                        ":bind2" => $_POST['companyname'],
                                        ":bind3" => $_POST['positionduties'],
                                        ":bind4" => $city,
                                        ":bind5" => $province
                                        );
                                        $alltuples = array (
                                                $tuple
                                        );
                                        executeBoundSQL("insert into positionforcompany values (:bind1, :bind2, :bind3, :bind4,
                                                        :bind5)", $alltuples);
                                        OCICommit($db_conn);
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
                printTypes($types);
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


                echo "<p>Location</p>";
                $locations = executePlainSQL("select city, province from location");
                printLocationNames($locations);

                
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
                $position = executePlainSQL("select * from positionforcompany");
                //$department = executePlainSQL("select * from department");
                $skills = executePlainSQL("select * from skill");
                //$location = executePlainSQL("select * from location");
                //$companytype = executePlainSQL("select * from companytype");
                printReviews($review);
                //printCompanyType($companytype);
                printCompany($company);
                printPosition($position);
                //printDepartment($department);
                printSkills($skills);
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
