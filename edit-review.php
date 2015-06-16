<?php
require 'functions.php';
include 'header.php';
$success = True; //keep track of errors so it redirects the page only if there are no errors
$db_conn = OCILogon($core_oracle_user, $core_oracle_password, "ug")

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
                                else if (empty($_POST['rating'])) {
                                        $Error = "Rating cannot be empty.";
                                        $success = false;
                                }                        

}

echo '<div class="error">' . $Error . '</div>';
?>


<div class="edit_review form">
        <h3>Edit Review:</h3>

        <?php
        
        if ($db_conn) {
        $tuple = array (
                ":bind1" => $_GET['rid']);
        $alltuples = array ($tuple);

        $review = executeBoundSQL("select * from review where rid= :bind1", $alltuples);

        while ($row = OCI_Fetch_Array($review, OCI_BOTH)) {

                echo "<form method='POST' action='edit-review.php'> <p>Company Name</p>";
                echo "<p>Company Name</p>";
                $companies = executePlainSQL("select * from coopcompany");
                printCompanyNames($companies, $row["COMPANYNAME"]);

                echo "<p>Position Title</p>";
                $postitles = executePlainSQL("select * from positionforcompany");
                printPosTitles($postitles, $row["POSTITLE"]);


                echo "<p>Rating</p>";
                echo "<input type='text' name='rating' value='" . $row["RATING"] ."' />";

                echo "<p>Review Body</p> ";
                echo "<p><textarea name='review_comment'>" . $row["REVIEW_COMMENT"] . "</textarea></p>"; 
                echo "<input type='hidden' name='rid' value=" . $row["RID"] . "/>";
                echo "<input type='submit' value='Update Review' name='submit_edited_review'></p>";
                echo "<input type='submit' value='Delete Review' name='delete_review'></p></form>";
                

        }
        echo "<a href='user-profile.php'>Cancel</a>";
        if (array_key_exists('submit_edited_review', $_POST)) {
                if (empty($_POST['review_comment'])) {
                        $Error = "review comment cannot be empty";
                }
                else {
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
                        echo($tuple);
                        executeBoundSQL("update review 
                                         set review_comment=:bind1, review_date=:bind2, companyname=:bind3, postitle=:bind4,
                                        rating=:bind5 where rid=:bind6", $alltuples);
                        OCICommit($db_conn);
                }

        }
        else if (array_key_exists('delete_review', $_POST)) {
                
                $tuple = array (
                        ":bind1" => intval($_POST['rid']));
                $alltuples = array($tuple);
                executeBoundSQL("delete from review where rid=:bind1", $alltuples);               
                OCICommit($db_conn);
        }

        if ($_POST && $success) {
                header("location: user-profile.php");
        } 
        OCILogoff($db_conn);
}
        ?>
</div>
