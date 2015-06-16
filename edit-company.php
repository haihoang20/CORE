<?php
require 'functions.php';
include 'header.php';
$success = True; //keep track of errors so it redirects the page only if there are no errors
$db_conn = OCILogon($core_oracle_user, $core_oracle_password, "ug");


?>

<div class="edit_company form">
        <?php
        
        if ($db_conn) {
        $tuple = array (
                ":bind1" => $_GET['name']);
        $alltuples = array ($tuple);

        $company = executeBoundSQL("select * from coopcompany where name= :bind1", $alltuples);

        while ($row = OCI_Fetch_Array($company, OCI_BOTH)) {

                echo "<form method='POST' action='edit-company.php'>";
                echo "<h3>Editing " . $row["NAME"] . "</h3>";
                //echo "<p>Company Name</p>";
                //echo "<input type='text' name='name' value='" . $row["NAME"] ."' />";

                echo "<p>Company Type</p>";
                $types = executePlainSQL("select type from companytype");
                printTypeNames($types, $row["TYPE"]);

                echo "<p>About Company</p> ";
                echo "<p><textarea name='about_company'>" . $row["ABOUT"] . "</textarea></p>"; 
                echo "<input type='hidden' name='old_name' value='" . $row["NAME"] . "'/>";
                echo "<input type='submit' value='Update Company' name='submit_edited_company'></p>";
                //echo "<input type='submit' value='Delete Company' name='delete_company'></p></form>";
                

        }
        echo "<a href='user-profile.php'>Cancel</a>";
        if (array_key_exists('submit_edited_company', $_POST)) {
                     $tuple = array (
                        ":bind1" => $_POST['about_company'],
                        ":bind2" => $_POST['company_type'],
                        ":bind3" => $_POST['old_name']
                        );
                        $alltuples = array (
                                $tuple
                        );
                        executeBoundSQL("update coopcompany set about=:bind1, type=:bind2 where name=:bind3", $alltuples);
                        OCICommit($db_conn);
                

        }
        else if (array_key_exists('delete_company', $_POST)) {
                
                $tuple = array (
                        ":bind1" => $_POST['oldname']);
                $alltuples = array($tuple);
                executeBoundSQL("delete from coopcompany where name=:bind1", $alltuples);               
                OCICommit($db_conn);
        }

        if ($_POST && $success) {
                header("location: user-profile.php");
        } 
        OCILogoff($db_conn);
}
        ?>
</div>
