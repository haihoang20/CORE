<?php
require 'functions.php';
include 'header.php';
$success = True; //keep track of errors so it redirects the page only if there are no errors
$db_conn = OCILogon($core_oracle_user, $core_oracle_password, "ug");

$Error = "";
$message = "";
$ID = 0;
if (array_key_exists('rid', $_GET)) {$ID = $_GET['rid'];}
if (array_key_exists('rid', $_POST)) {$ID = $_POST['rid'];}

if (array_key_exists('submit_edited_review', $_POST)) {
                if ('---' == $_POST['companyname']) {
                        $Error = "Please select a company name.";
                        $success = false;
                }

                else if (empty($_POST['review_comment'])) {
                        $Error = "Review body cannot be empty.";
                        $success = false;
                }                        
                else if ('---' != $_POST['postitle']) {
                        $comp = array ( array ( ":bind1" => $_POST['postitle']));
                        $query = executeBoundSQL("select cname from positionforcompany where title=:bind1", $comp);
                        $row = OCI_Fetch_Array($query, OCI_BOTH);
                        if ($row['CNAME'] != $_POST['companyname']) {
                                $success=false;
                                $Error = "The chosen position is not at the company you have selected.";        
                        }
                }
}
if ($db_conn) {
        if ($success) {
                if (array_key_exists('submit_edited_review', $_POST)) {
                             $tuple = array (
                                ":bind1" => $_POST['review_comment'],
                                ":bind2" =>date("M d Y, g:ia "),
                                ":bind3" => $_POST['companyname'],
                                //":bind4" => 101, // dummy value, coop student id
                                ":bind4" => $_POST['postitle'],
                                ":bind5" => intval($_POST['rating']),
                                ":bind6" => intval($_POST['rid'])
                                );
                                $alltuples = array (
                                        $tuple
                                );
                                executeBoundSQL("update review 
                                                 set review_comment=:bind1, review_date=:bind2, companyname=:bind3, postitle=:bind4,
                                                rating=:bind5 where rid=:bind6", $alltuples);
                                OCICommit($db_conn);
                        

                }
                else if (array_key_exists('delete_review', $_POST)) {
                        
                        $tuple = array (
                                ":bind1" => intval($_POST['rid']));
                        $alltuples = array($tuple);
                        executeBoundSQL("delete from review where rid=:bind1", $alltuples);               
                        OCICommit($db_conn);
                }
        }

        if ($_POST && $success) {
                $message = "Success!";
                //header("location: user-profile.php");
        }

echo '<div class="error">' . $Error . '</div>';
echo '<div class="success">' .$message . '</div>';
?>

<div class="edit_review form">
        <h3>Edit Review:</h3>

        <?php
        
        $tuple = array (
                ":bind1" => $ID );
        $alltuples = array ($tuple);

        $review = executeBoundSQL("select * from review where rid= :bind1", $alltuples);

        while ($row = OCI_Fetch_Array($review, OCI_BOTH)) {

                echo "<form method='POST' action='edit-review.php'> <p>Company Name</p>";
                $companies = executePlainSQL("select name from coopcompany");
                printCompanyNames($companies, $row["COMPANYNAME"]);

                echo "<p>Position Title</p>";
                $postitles = executePlainSQL("select * from positionforcompany");
                printPosTitles($postitles, $row["POSTITLE"]);


                echo "<p>Rating</p>";
                echo "<input type='text' name='rating' value='" . $row["RATING"] ."' />";

                echo "<p>Review Body</p> ";
                echo "<p><textarea name='review_comment'>" . $row["REVIEW_COMMENT"] . "</textarea></p>"; 
                echo "<input type='hidden' name='rid' value='" . $row["RID"] . "'/>";
                echo "<input type='submit' value='Update Review' name='submit_edited_review'></p>";
                echo "<input type='submit' value='Delete Review' name='delete_review'></p></form>";
                

        }
        echo "<a href='user-profile.php'>Cancel</a>";
 
        OCILogoff($db_conn);
}

echo "<script>";
echo "gapi.load('auth2',function(){gapi.auth2.init();});";
echo "</script>";
        ?>
</div>
