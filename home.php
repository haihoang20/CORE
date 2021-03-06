
<!-- CORE HOME PAGE -->

<?php
require 'home-print-functions.php';
include 'header.php';
?>

<!-- LAYOUT AND FORMS FOR CORE HOME PAGE -->

<!-- Simple Search of Reviews -->
<div class="home">
<h2>Home</h2>


<div class="simple_search form search">
  <h3> Simple search of the reviews: <br /></h3>
  <p>Search for company name, position title, or a comment containing... </p>
  <form method="GET" action="home.php">
  <p><input type="text" name="searchPhrase" size="6"><br /><br />
  	<font size="2">What information would you like to view?<br />
  	<input type='checkbox' name='attribute[]' id='attribute' value='REVIEW_DATE'/>Date<br />
  	<input type='checkbox' name='attribute[]' id='attribute' value='COMPANYNAME'/>Company<br />
  	<input type='checkbox' name='attribute[]' id='attribute' value='POSTITLE'/>Position<br />
  	<input type='checkbox' name='attribute[]' id='attribute' value='RATING'/>Rating<br />
  	<input type='checkbox' name='attribute[]' id='attribute' value='REVIEW_COMMENT'/>Comment<br />
  </font>
  <input type="submit" value="Simple Search" name="simplesearch"></p>
  </form>
<!-- </div> -->

<!-- Advanced Search of Reviews -->
  <h3> Advanced search of the reviews: </h3>
  <form method="GET" action="home.php" class="advanced_search">
<p>
<div>  <label> Company Name contains </label><input type="text" name="companyname" size="6"></div>
<div>  <br><label> Company Type/Industry </label><input type="text" name="ctype" size="6"></div>
<div>  <br><label> Position Title contains </label><input type="text" name="postitle" size="6"></div>
<div>  <br><label> Minimum Rating </label><input type="text" name="rating" size="6"></div>
<div>  <br><label> Earliest Written Date (YYYY-MM-DD) </label><input type="text" name="datebound" size="6"></div>
<div>  <br><label> Comment contains </label><input type="text" name="commentcontains" size="6"></div>
<div>  <br><label> Skills desired (separate each by comma), at least one of </label><input type="text" name="skillsreq" size="6"></div>
<div>  <br><label> City </label><input type="text" name="city" size="6"></div>
<div>  <br><label> Province/State </label><input type="text" name="province" size="6"></div>
<div>  <br><label> Country </label><input type="text" name="country" size="6"></div>
  </p><p><input type="submit" value="Advanced Search" name="advsearch"></p>
  </form>
</div>

<!-- Simple Table Views -->
<div class="query_buttons form">
  <h3>View of Tables</h3>
  <form method="GET" action="home.php">
  <input type="submit" value="Reviews" name="getreviews">
  <input type="submit" value="Companies" name="getcompanies">
  <input type="submit" value="Positions" name="getpositions">
  <input type="submit" value="Company Types" name="getcompanytypes">
  <input type="submit" value="Departments" name="getdepartments">
  <input type="submit" value="Skills" name="getskills">
  <input type="submit" value="Locations" name="getlocations">
  <input type="submit" value="Company Locations" name="getcompanylocations">
  </form>

<p>
  <form method="GET" action="home.php">
    Companies with ratings:
    <input type="submit" value="1" name="getrating">
    <input type="submit" value="2" name="getrating">
    <input type="submit" value="3" name="getrating">
    <input type="submit" value="4" name="getrating">
    <input type="submit" value="5" name="getrating">
  </form>
</p>

<form method="GET" action="home.php">
  <input type="submit" value="Most Popular Vancouver Companies" name="getvancom">
  <input type="submit" value="Departments With Most Jobs" name="deptjobs">
  <input type="submit" value="Top 5 Desired Skills" name="topskills">
</form>

<!-- Search for skillset: query with division -->
<form method="GET" action="home.php">
	<p><input type="submit" value="Search for jobs that require an exact skillset" name="skillsetqueryprep"/></p>
</form>
</div>

<script>
	
   

   if(getCookie("reloaded")== null){
   	  document.cookie="reloaded=true"; 
  	  window.location.href = "http://www.ugrad.cs.ubc.ca/~n6o8/home.php";
   }

  function getCookie(name) {
    var dc = document.cookie;
    var prefix = name + "=";
    var begin = dc.indexOf("; " + prefix);
    if (begin == -1) {
        begin = dc.indexOf(prefix);
        if (begin != 0) return null;
    }
    else
    {
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

  var myCookie = getCookie("user");
  if(myCookie == null){
  	document.cookie="user=" + window.location.hash.substr(1,window.location.hash.length-1);
  }

</script>




<?php

$success = True; //keep track of errors so it redirects the page only if there are no errors
$db_conn = OCILogon($core_oracle_user, $core_oracle_password, "ug");

$cookie_name = 'user';

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


$email =  $_COOKIE[$cookie_name];




if(checkAdmin($email)){
	echo "<script>document.cookie=\"admin=yes\"</script>";
}else if(!isRegistered($email)){
	echo "<script>window.location.href=\"http://www.ugrad.cs.ubc.ca/~n6o8/register.php\";</script>";
}


echo "<script>";
echo "gapi.load('auth2',function(){gapi.auth2.init();});";
echo "</script>";

// Search for a given keyword or phrase contained in either the company name, position title, or review comments
// and the selected attributes/information to show
function simpleSearch($sphrase, $attrsToShow) {
	$sphrase = "'%".$sphrase."%'";
	$selectwhat = '';
	$tableheader = '<tr>';
	$groupby = 'group by '; // in order to get distinct tuples
	foreach ($attrsToShow as $attr) {
		if (!empty($selectwhat)) {
			$selectwhat = $selectwhat.", ";
			$groupby = $groupby.", ";
		}
		$selectwhat = $selectwhat.$attr;
		$tableheader = $tableheader."<th>".$attr."</th>";
		$groupby = $groupby.$attr;
	}
	$tableheader = $tableheader."</tr>";

	if (empty($selectwhat)) {
		$selectwhat = "*";
		$groupby = "";
	}
	$sqlquery = "select $selectwhat
				 from review
				 where companyname like $sphrase or postitle like $sphrase or review_comment like $sphrase $groupby";
	$results = executePlainSQL($sqlquery);

	// Print results in table
	if ($selectwhat == "*") {
		printReviews($results);
	} else {
		echo "<br>Reviews:<br>";
		echo "<table>";
		echo $tableheader;
		while ($row = OCI_Fetch_Array($results, OCI_BOTH)) {
			$rows = '<tr>';
			foreach ($attrsToShow as $attr) {
				$rows = $rows."<td>".$row[$attr]."</td>";
			}
			$rows = $rows."</tr>";
			echo $rows;
		}
		echo "</table>";
	}
}

// Helper function for advanced search
// Adds an ' and ' if needed between selection criteria
function helperAddOptionalAnd($var) {
	if (!empty($var)) {
		$var = $var." and ";
	}
	return $var;
}

/*
 * Search for reviews satisfying any subset of given criteria:
 * - word/phrase contained in the company name, company type/industry, position title, review comment, skills required
 * - rating >= to a given rating
 * - date >= a given date
 *- companies located in given city, province, and/or country
 */
function advancedSearch($cname, $ctype, $postitle, $rating, $ccontains, $dateb, $skills, $city, $prov, $country) {
	$selectwhat = '*';
	$andfrom = '';
	$reqs = '';
	if (!empty($cname)) {
		$reqs = $reqs."r.companyname LIKE '%$cname%'";
	}
	if (!empty($postitle)) {
		$reqs = helperAddOptionalAnd($reqs);
		$reqs = $reqs."r.postitle like '%$postitle%'";
	}
	if (!empty($rating)) {
		$reqs = helperAddOptionalAnd($reqs);
		$reqs = $reqs."r.rating >=".(int)$rating;
	}
	if (!empty($dateb)) {
		$reqs = helperAddOptionalAnd($reqs);
		$reqs = $reqs."r.review_date >= '$dateb'";
	}
	if (!empty($ccontains)) {
		$reqs = helperAddOptionalAnd($reqs);
		$reqs = $reqs."r.review_comment like '%$ccontains%'";
	}
	if (!empty($skills)) {
		$selectwhat = "r.rid, r.review_comment, r.review_date, r.companyname, r.postitle, r.rating";
		$andfrom = ", validpostitlecname vpc";
		$reqs = helperAddOptionalAnd($reqs);
		$reqs = $reqs."r.companyname = vpc.cname and r.postitle = vpc.postitle";

		$skillsArray = explode(',', $skills);
		$sqlskills = "";
		foreach ($skillsArray as $key=>$value) {
			$skillsArray[$key] = "'%".$value."%'";
			if (!empty($sqlskills)) {
				$sqlskills = $sqlskills." or ";
			}
			$sqlskills = $sqlskills."sname like $skillsArray[$key]";
		}
		$sqlmakeview = "create view validpostitlecname as (select ptitle as postitle, cname
														   from positionrequiresskill
														   where sname in (select name as sname
														   				   from skill
														   				   where $sqlskills))";
		executePlainSQL($sqlmakeview);
	}
	if (!empty($city)) {
		$andfrom = $andfrom.", companylocation cl";
		$reqs = helperAddOptionalAnd($reqs);
		$reqs = $reqs."r.companyname = cl.cname and cl.city = '$city'";
	}
	if (!empty($prov)) {
		$andfrom = $andfrom.", companylocation cl2";
		$reqs = helperAddOptionalAnd($reqs);
		$reqs = $reqs."r.companyname = cl2.cname and cl2.province = '$prov'";
	}
	if (!empty($country)) {
		$andfrom = $andfrom.", companylocation cl3, location l";
		$reqs = helperAddOptionalAnd($reqs);
		$reqs = $reqs."r.companyname = cl3.cname and cl3.city = l.city and cl3.province = l.province and l.country = '$country'";
	}
	if (!empty($ctype)) {
		$andfrom = $andfrom.", coopcompany cc";
		$reqs = helperAddOptionalAnd($reqs);
		$reqs = $reqs."r.companyname = cc.name and cc.type = '$ctype'";
	}

	if (!empty($reqs)) {
		$reqs = "where ".$reqs;	// for the case where no selection input was provided
	}
	$sqlquery = "select $selectwhat
				 from review r $andfrom
				 $reqs";
	echo $sqlquery;
	$results = executePlainSQL($sqlquery);
	printReviews($results);
	if (!empty($skills)) {
		executePlainSQL("drop view validpostitlecname");
	}
}

// Helper method for advanced search to check type of date inputted by user
// Checks if the date conforms to a 'YYYY-MM-DD' format
function isValidDateBound($dateb) {
	$datebarray = explode('-', $dateb); // var used for type checking of dateb
	if (count($datebarray) != 3) return false;
	foreach ($datebarray as $d) {
		if (!is_numeric($d)) return false;
	}
	if (strlen($datebarray[0]) != 4) return false;
	if (strlen($datebarray[1]) != 2 || $datebarray[1] < 1 || $datebarray[1] > 12) return false;
	if (strlen($datebarray[2]) != 2 || $datebarray[2] < 1 || $datebarray[2] > 31) return false;
	return true;
}

/*
 * Search for company positions that require an exact skillset
 * (Division Query)
 */
function skillsetSearch($skillset) {
	$p1 = '';
	$p2 = '';
	echo "<p>You selected the following skillset: <br />";
	foreach ($skillset as $s) {
		echo $s."<br />";
		if (!empty($p1))
			$p1 = $p1." or";
		$p1 = $p1." s.name = '".$s."'";
		if (!empty($p2))
			$p2 = $p2." or";
		$p2 = $p2." prs.sname = '".$s."'";
	}
	echo "</p>";

	$viewqry = "create view invalidposskill as (select pfc.cname, pfc.title, s.name as sname
												from positionforcompany pfc, skill s
												where $p1
												minus
												(select prs.cname, prs.ptitle, prs.sname
												 from positionrequiresskill prs
												 where $p2))";
	executePlainSQL($viewqry);
	$qry = "select cname, title
			from positionforcompany
			minus
			select cname, title
			from invalidposskill";
	$results = executePlainSQL($qry);
	executePlainSQL("drop view invalidposskill");

	// Display the Results
    echo "<div class='results'";
    printPosition($results);
	echo "</div>";
}

// Gets all tuples and attributes from a given table
function getAllFromTable($tablename) {
	return executePlainSQL("select * from ".$tablename);
}

// Get all companies who received at least one review with rating n
function getCompaniesWithRatingN($n) {
	$company = executePlainSQL("select distinct name, about, type
    							from coopcompany cc, review r
    							where cc.name=r.companyname and r.rating=".$n);
    printCompany($company);
    OCICommit($db_conn);
}

// Connect Oracle...
if ($db_conn) {

	if (array_key_exists('simplesearch', $_GET)) {
			$sphrase = $_GET['searchPhrase'];
			$attrsToShow = $_GET['attribute'];

			simpleSearch($sphrase, $attrsToShow);
			OCICommit($db_conn);
	} else
	if (array_key_exists('advsearch', $_GET)) {
		$cname = $_GET['companyname'];
		$ctype = $_GET['ctype'];
		$postitle = $_GET['postitle'];
		$rating = $_GET['rating'];
		$ccontains = $_GET['commentcontains'];
		$dateb = $_GET['datebound'];
		$skills = $_GET['skillsreq'];
		$city = $_GET['city'];
		$prov = $_GET['province'];
		$country = $_GET['country'];

		if (!empty($dateb) && !isValidDateBound($dateb)) {
			echo "<br>Advanced Search: Please enter a valid date as YYYY-MM-DD. <br>";
		} else if (!empty($rating) && (!is_numeric($rating) || $rating < 1 || $rating > 5)) {
			echo "<br>Advanced Search: Please enter a number between 1 and 5 for the rating field.<br>";
		} else {
			advancedSearch($cname, $ctype, $postitle, $rating, $ccontains, $dateb, $skills, $city, $prov, $country);
		}
		OCICommit($db_conn);
	} else
	if (array_key_exists('skillsetqueryprep', $_GET)) {
		$skills = executePlainSQL("select name from skill");

        echo "<div class='results'>";
		echo "<br>Skills:<br>";
		echo "<form method='GET' action='home.php'>";
		while ($row = OCI_Fetch_Array($skills, OCI_BOTH)) {
			echo "<input type='checkbox' name='skill[]' id='skill' value='".$row["NAME"]."'/>".$row["NAME"]."<br />";
		}
		echo "<p><input type='submit' value='Submit' name='skillsetsearch'></p></form>";
                echo "</div>";
	} else
	if (array_key_exists('skillsetsearch', $_GET)) {
		$ss = $_GET['skill'];
		if (empty($ss)) {
			echo "<br>Skill Set Search: <br> Please select at least one skill. <br>";
		} else {
			skillsetsearch($ss);
		}
		OCICommit($db_conn);
	} else
      if (array_key_exists('getreviews', $_GET)){
        printReviews(getAllFromTable("review"));
        OCICommit($db_conn);
      } else
      if (array_key_exists('getcompanies', $_GET)){
      	printCompany(getAllFromTable("coopcompany"));
      	OCICommit($db_conn);
      } else
      if (array_key_exists('getpositions', $_GET)){
        printPosition(getAllFromTable("positionforcompany"));
        OCICommit($db_conn);
      } else
      if (array_key_exists('getcompanytypes', $_GET)){
        printCompanyType(getAllFromTable("companytype"));
        OCICommit($db_conn);
      } else
      if (array_key_exists('getdepartments', $_GET)){
        printDepartment(getAllFromTable("department"));
        OCICommit($db_conn);
      } else
      if (array_key_exists('getskills', $_GET)){
        printSkills(getAllFromTable("skill"));
        OCICommit($db_conn);
      } else
      if (array_key_exists('getlocations', $_GET)){
        printLocation(getAllFromTable("location"));
        OCICommit($db_conn);
      } else
      if (array_key_exists('getcompanylocations', $_GET)){
        printCompanyLocation(getAllFromTable("companylocation"));
      } else
      if (array_key_exists('getrating', $_GET)){
      	getCompaniesWithRatingN($_GET['getrating']);
      } else
      if (array_key_exists('getvancom', $_GET)){
        $company = executePlainSQL("select distinct name, about, type
        							from coopcompany cc, companylocation cl, review r
        							where cc.name=cl.cname and cc.name=r.companyname and cl.city='Vancouver' and r.rating>=4");
        printCompany($company);
        OCICommit($db_conn);
      } else
      if (array_key_exists('deptjobs', $_GET)){
        executePlainSql("drop view temp");
        executePlainSql("create view Temp(cname, poscount) as (select cname, COUNT(*) as poscount
        													                             from PositionForCompany
        													                             GROUP BY cname)");
        executePlainSql("drop view temp2");
        executePlainSql("create view Temp2(dname, num) as (select dname, max(posc) as num
                                                            from (select dname, sum(poscount) as posc
                                                                  from companyhiresfordept c, temp t
                                                                  where t.cname=c.cname
                                                                  group by dname)
                                                            group by dname)");
         $topdept = executePlainSQL("select dname, num
                                     from Temp2
                                     where num>=all(select num
                                                    from Temp2)");

        printTopDepartment($topdept);
        OCICommit($db_conn);
      } else
      if (array_key_exists('topskills', $_GET)){
        $topskills = executePlainSQL("select name, num
                                      from (select sname as name, count(*) as num
                                            from positionrequiresskill
                                            group by sname
                                            order by count(*) desc)
                                      where rownum<=5");
        printTopSkills($topskills);
        OCICommit($db_conn);
      }

	if ($_POST && $success) {
		//POST-REDIRECT-GET -- See http://en.wikipedia.org/wiki/Post/Redirect/Get
		header("location: home.php");
	} else {
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
