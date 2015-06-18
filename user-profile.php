<script async>

    function getCookie(name) {
        var dc = document.cookie;
        var prefix = name + "=";
        var begin = dc.indexOf("; " + prefix);
        if (begin == -1) {
            begin = dc.indexOf(prefix);
            if (begin != 0) return null;
        }else{
            begin += 2;
            var end = document.cookie.indexOf(";", begin);
            if (end == -1) {
                end = dc.length;
            }
        }
    
        return unescape(dc.substring(begin + prefix.length, end));
    }

    var validCookie = getCookie("valid");
    if(validCookie == null){
        window.location.href = "http://www.ugrad.cs.ubc.ca/~n6o8/landing_page.php";
  }     
    
</script>
<?php
require 'user-profile-functions.php';
include 'header.php';
$success = True; //keep track of errors so it redirects the page only if there are no errors
$db_conn = OCILogon($core_oracle_user, $core_oracle_password, "ug");

$email =  $_COOKIE['user'];

$isAdmin = (empty($_COOKIE['admin'])) ? false : true;
$query_string = "select * from coopstudent where email='" . $email . "'";
$query_user = executePlainSQL($query_string);
$row = OCI_Fetch_Array($query_user, OCI_BOTH);
$coop_id = $row["ID"];
$coop_name = $row["NAME"];
$coop_email = $row["EMAIL"];
$coop_year = $row["YEAR"];
$coop_dname = $row["DNAME"];

function checkAdmin($email){
    $email_list = executePlainSQL("select email from admin");

        while ($row = OCI_Fetch_Array($email_list, OCI_BOTH)) {
            if ($row["EMAIL"] == $email){
                return true;
                break;
            }
        }
        return false;
    
}


function isRegistered($email){
    $email_list = executePlainSQL("select email from coopstudent");
        while ($row = OCI_Fetch_Array($email_list, OCI_BOTH)) {
            if ($row["EMAIL"] == $email){
                return true;
                break;
            }
        }
        return false;
    
}

if(!checkAdmin($email) && !isRegistered($email)){
    echo "<script>window.location.href=\"http://www.ugrad.cs.ubc.ca/~n6o8/register.php\";</script>";
}


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

                }
        else if (array_key_exists('add_comphiresfromdept', $_POST)) {
                                if (empty($_POST['companyname'])) {
                                        $Error = "Company name cannot be empty.";
                                        $success = false;
                                }
                                else if (empty($_POST['departmentname'])) {
                                        $Error = "Department name cannot be empty.";
                                        $success = false;
                                }
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
                                                ":bind2" =>date("Y-m-d"),
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

                                              foreach ($_POST['department'] as $dept) {
                                                $s = array ( 
                                                        array ( 
                                                                ":bind1" => $_POST['companyname'],
                                                                ":bind2" => $dept 
                                                         ));
                                             executeBoundSQL("insert into companyhiresfordept values(:bind1, :bind2)", $s);
                                             }

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

                        //else if (array_key_exists('add_comphiresfromdept', $_POST)) {
                        //                     $tuple = array (
                        //                        ":bind1" => $_POST['companyname'],
                        //                        ":bind2" => $_POST['departmentname'],
                        //                     );
                        //                     $alltuples = array (
                        //                        $tuple
                        //                     );
                        //                     executeBoundSQL("insert into companyhiresfordept values (:bind1, :bind2)", $alltuples);
                        //                     OCICommit($db_conn);
                        //}
        }

    if ($_POST && $success) {
                header("location: user-profile.php");
                exit;
    } else {

echo '<div class="error">' . $Error . '</div>';
?>
<div class="user_profile">
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

        
                echo "<p>Company Hires From</p>";
                $departments = executePlainSQL("select name from department");
                printDepartmentNamesMulti($departments);
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

echo "<script>";
echo "gapi.load('auth2',function(){gapi.auth2.init();});";
echo "</script>";

if($isAdmin){
    $review = executePlainSQL("select * from review");
    $users = executePlainSQL("select * from coopstudent");

    echo "<br>Coop Students:<br>";
                echo "<table>";
                echo "<tr><th>Name</th></tr>";
                while ($user = OCI_Fetch_Array($users, OCI_BOTH)) {
                        echo "<tr><td>" . $user['NAME'] . "</td><td><a href='edit-coopstudent.php?email=" . $user["EMAIL"] . "'>Edit User</a></td></tr>";
                }
                echo "</table>";
}else{
    $review = executePlainSQL("select * from review where coopstudid= " . $coop_id);
}


                $review = executePlainSQL("select * from review");
                $skills = executePlainSQL("select * from skill");
                printReviews($review);
                
                printSkills($skills);
                
                /****** Print Company Stuff **********/

                echo "<h3>Companies:</h3>";
                echo "<table>";
                echo "<tr><th>Name</th><th>About</th><th>Type</th><th>Hires From</th></tr>";

                $companies = executePlainSQL("select * from coopcompany");

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
                        echo "<td><a href='edit-company.php?name=" . $company["NAME"] . "'>Edit Company</a></td>";
                        echo "</tr>";
                }
                echo "</table>";

                /****** Print Positions Stuff ********/
                echo "<h3>Positions:</h3>";
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
                        echo "<td><a href='edit-position.php?title=" . $position['TITLE'] . "&cname=" . $position['CNAME'] ."'>Edit Position</a></td>";
                        echo "</tr>";
                }
                echo "</table>";

                if (!$isAdmin){
                    echo "<br>Your Profile Information:<br>";
                    echo "<table>";
                    echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Year</th><th>Department</th></tr>";
                    echo "<tr><td>" . $coop_id. "</td><td>" . $coop_name . "</td><td>" . $coop_email . "</td><td>" . $coop_year. "</td><td>" . $coop_dname. "</td><td><a href='edit-coopstudent.php?email=" . $coop_email . "'>Edit Profile</a></td></tr>";
                    echo "</table>";
                }



    }

    //Commit to save changes...
    OCILogoff($db_conn);
} else {
    echo "cannot connect";
    $e = OCI_Error(); // For OCILogon errors pass no handle
    echo htmlentities($e['message']);
  }
                
?>
</div>