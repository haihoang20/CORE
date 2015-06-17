<?php
    require 'functions.php';
    include 'header.php';
?>


<div class="register_form form">
        <h3>Sign up for the CORE:</h3>
        <form method="POST" action="register.php">

        <p>Name</p>
        <input type="text" name="student_name" />

        <p>Email</p>
        <p id="email_id"></p>

        <p>Department</p>
        <?php
        $db_conn = OCILogon($core_oracle_user, $core_oracle_password, "ug");
        if ($db_conn) {
                $departments = executePlainSQL("select name from department");
                printDepartmentNames($departments);
        }
        ?>

        <p>Year</p>
        <input type="text" name="student_year" />

    
        <input type="submit" value="Confirm Profile" name="submit_profile"></p>
        </form>
</div>

<script>
function getUserCookieValue(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
    }
    return "";
}

var email = getUserCookieValue("user");
var email_id = document.getElementById("email_id");
email_id.innerHTML = email;


</script>

<?php

echo "<script>";
echo "gapi.load('auth2',function(){gapi.auth2.init();});";
echo "</script>";

$success = True;

$Error = "";
        if (array_key_exists('submit_profile', $_POST)) {
                        
            if ('---' == $_POST['departmentname']) {
                $Error = "Please select a department.";
                $success = false;
            }
        

            else if (empty($_POST['student_name'])) {
                $Error = "Name field cannot be empty.";
                $success = false;
            }

            else if (empty($_POST['student_year'])) {
                $Error = "Year field cannot be empty.";
                $success = false;
            }
        }

       



if ($db_conn) {
                if ($success) {
                        if (array_key_exists('submit_profile', $_POST)) {
                                             $dept_name= $_POST['departmentname'];
                                             if ('---' == $dept_name) {
                                                     $dept_name = null;
                                             }
                                        
                                             $tuple = array (
                                                ":bind1" => $_POST['student_name'],
                                                ":bind2" => $_COOKIE['user'],
                                                ":bind3" => $_POST['student_year'],
                                                ":bind4" => $_POST['departmentname']
                                             );
                                             $alltuples = array (
                                                     $tuple
                                             );
                                             echo($tuple);
                                             executeBoundSQL("insert into coopstudent values ((select c1.id from coopstudent c1 where not exists (select c2.id from coopstudent c2 where c2.id > c1.id)) + 1, :bind1, :bind2, :bind3, :bind4)", $alltuples);
                                             OCICommit($db_conn);

                        } 
                }
            }

    if ($_POST && $success) {
                header("location: home.php");
                exit;
    }

    echo '<div class="error">' . $Error . '</div>';

?>

