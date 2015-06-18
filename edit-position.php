<?php
require 'user-profile-functions.php';
include 'header.php';
$success = True; //keep track of errors so it redirects the page only if there are no errors
$db_conn = OCILogon($core_oracle_user, $core_oracle_password, "ug");
$message = "";
$Error = "";


$TITLE = "";
$CNAME = "";
if (array_key_exists('title', $_GET)) {$TITLE = $_GET['title'];}
if (array_key_exists('cname', $_GET)) {$CNAME = $_GET['cname'];}
if (array_key_exists('old_title', $_POST)) {$TITLE = $_POST['old_title'];}
if (array_key_exists('old_cname', $_POST)) {$CNAME = $_POST['old_cname'];}


if (array_key_exists('submit_edited_position', $_POST)) {
                if (empty($_POST['position_duties'])) {
                        $Error = "'Position Duties' cannot be empty.";
                        $success = false;
                }
}


if ($db_conn) {
        if ($success) {
                if (array_key_exists('submit_edited_position', $_POST)) {
                             $tuple = array (
                                ":bind1" => $_POST['position_duties'],
                                ":bind2" => $_POST['old_cname'],
                                ":bind3" => $_POST['old_title']
                                );
                                $alltuples = array (
                                        $tuple
                                );
                                executeBoundSQL("update positionforcompany set duties=:bind1 where cname=:bind2 and title=:bind3", $alltuples);

                             executeBoundSQL("delete from positionrequiresskill where ptitle=:bind3 and cname=:bind2", $alltuples);
                             foreach ($_POST['skill'] as $skill) {
                                $s = array (
                                        array (
                                                ":bind1" => $_POST['old_title'],
                                                ":bind2" => $_POST['old_cname'],
                                                ":bind3" => $skill
                                        ));
                                        executeBoundSQL("insert into positionrequiresskill values(:bind1, :bind2, :bind3)", $s);
                                }
                                     
                             OCICommit($db_conn);
                        

                }
                else if (array_key_exists('delete_position', $_POST)) {
                        
                        $tuple = array (
                                ":bind1" => $_POST['old_cname'],
                                ":bind2" => $_POST['old_title']);
                        $alltuples = array($tuple);
                        executeBoundSQL("delete from positionforcompany where cname=:bind1 and title=:bind2", $alltuples);               
                        OCICommit($db_conn);
                }

        }

        if ($_POST && $success) {
                $message="Success!";
        } 


echo '<div class="error">' . $Error . '</div>';
echo '<div class="success">' .$message . '</div>';

?>

<div class="edit_position form">
        <?php
        
        $tuple = array (
                ":bind1" => $TITLE,
                ":bind2" => $CNAME);
        $alltuples = array ($tuple);

        $position = executeBoundSQL("select * from positionforcompany where title= :bind1 and cname=:bind2", $alltuples);

        while ($row = OCI_Fetch_Array($position, OCI_BOTH)) {

                echo "<form method='POST' action='edit-position.php'>";
                echo "<h3>Editing Position " . $row['TITLE'] . " - " . $row['CNAME'] . "</h3>";

                echo "<p>Duties</p> ";
                echo "<p><textarea name='position_duties'>" . $row["DUTIES"] . "</textarea></p>";
                
                echo "<p>Required Skills</p>";
                $allSkills = executePlainSQL("select name from skill");                 
                
                $skillsArray = array();
                $currentSkills = executeBoundSQL("select name from skill s, positionrequiresskill p where s.name = p.sname and p.ptitle=:bind1 and p.cname =:bind2", $alltuples);                 
                while ($sr= OCI_Fetch_Array($currentSkills, OCI_BOTH)) {
                        array_push($skillsArray, $sr['NAME']);
                } 
                printSkillNames($allSkills, $skillsArray);
                

                echo "<input type='hidden' name='old_title' value='" . $row["TITLE"] . "'/>";
                echo "<input type='hidden' name='old_cname' value='" . $row["CNAME"] . "'/>";
                echo "<input type='submit' value='Update Position' name='submit_edited_position'></p>";
                echo "<input type='submit' value='Delete Position' name='delete_position'></p></form>";
                

        }
        echo "<a href='user-profile.php'>Cancel</a>";

        OCILogoff($db_conn);
}

echo "<script>";
echo "gapi.load('auth2',function(){gapi.auth2.init();});";
echo "</script>";
        ?>
</div>

