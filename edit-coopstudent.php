<?php
require 'functions.php';
include 'header.php';
$success = True; //keep track of errors so it redirects the page only if there are no errors
$db_conn = OCILogon($core_oracle_user, $core_oracle_password, "ug");
$Error = "";

$coop_email = $_GET['email'];
$query_string = "select * from coopstudent where email='" . $coop_email . "'";
$query_user = executePlainSQL($query_string);
$row = OCI_Fetch_Array($query_user, OCI_BOTH);
$coop_name = $row["NAME"];
$coop_id = $row["ID"];
$coop_year = $row["YEAR"];
$coop_dname = $row["DNAME"];

$isAdmin = (empty($_COOKIE['admin'])) ? false : true;


if (array_key_exists('old_email', $_POST)) {$coop_email = $_POST['old_email'];}


if (array_key_exists('submit_edited_profile', $_POST)) {
                if ('---' == $_POST['departmentname']) {
                        $Error = "Please select a department.";
                        $success = false;
                }
                else if (empty($_POST['name'])) {
                        $Error = "Name field cannot be empty.";
                        $success = false;
                }else if (empty($_POST['year'])) {
                        $Error = "Year field cannot be empty.";
                        $success = false;
                }
}


if ($db_conn) {
        if ($success) {
                if (array_key_exists('submit_edited_profile', $_POST)) {
                             $tuple = array (
                          		":bind0" => $_POST['id'], //can't change id
                                ":bind1" => $_POST['name'],
                                ":bind2" => $_POST['email'],
                                ":bind3" => $_POST['year'],
                                ":bind4" => $_POST['departmentname']
                                );
                                $alltuples = array (
                                        $tuple
                                );
                                executeBoundSQL("update coopstudent set name=:bind1, email=:bind2, year=:bind3, dname=:bind4 where id=:bind0", $alltuples);
                                OCICommit($db_conn);
                        

                }
                else if (array_key_exists('delete_profile', $_POST)) {
                        
                        $tuple = array (
                                ":bind1" => $_POST['id']);
                        $alltuples = array($tuple);
                        executeBoundSQL("delete from coopstudent where id=:bind1", $alltuples);               
                        OCICommit($db_conn);
                }

        }

        if ($_POST && $success) {
        		header('Location: user-profile.php');
        		exit;
        } 


echo '<div class="error">' . $Error . '</div>';
echo '<div class="success">' .$message . '</div>';

?>

<div class="edit_profile form">
        <?php
        
                echo "<form method='POST' action='edit-coopstudent.php'>";
                echo "<h3>Editing " . $coop_name . "</h3>";
                //echo "<p>Company Name</p>";
                //echo "<input type='text' name='name' value='" . $row["NAME"] ."' />";

                echo "<p>Name</p>";
                echo "<input type='text' name='name' value='" . $coop_name ."' />";

                echo "<p>Email</p>";
                echo "<input type='text' name='email' value='" . $coop_email ."' />";

                echo "<p>Year</p>";
                echo "<input type='text' name='year' value='" . $coop_year ."' />";


                echo "<p>Departments</p>";
                $departments = executePlainSQL("select name from department");
                printDepartmentNames($departments);

                echo "<input type='hidden' name='id' value='" . $coop_id . "'/>";
                echo "<input type='hidden' name='old_email' value='" . $coop_email . "'/>";
                echo "<input type='submit' value='Update Profile' name='submit_edited_profile'>";
                
                if ($isAdmin){
                	echo "<input type='submit' value='Delete Profile' name='delete_profile'></p></form>";
                }
                

        echo "<a href='user-profile.php'>Cancel</a>";

        OCILogoff($db_conn);
}

echo "<script>";
echo "gapi.load('auth2',function(){gapi.auth2.init();});";
echo "</script>";
        ?>
</div>