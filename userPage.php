<html>
<head>
<title>User Page</title>
<link href="style/main.css" rel="stylesheet" type="text/css">
</head>
<body>
<div id="content">
<div class="card zdepth2 expanded">
<form>
  <input type="button" value="⬅ Back" onclick="history.back()">
</form>

<?php
$inputQuery = $_GET['User_ID'];
include_once("include/dbh.inc.php");

$sql = 
<<<EOL
SELECT User.User_ID, Fname, Lname, Movie.Movie_ID, Rating, Comment, Movie.Title
FROM `User`,`Review`,`Movie`
WHERE User.User_ID=Review.User_ID AND Movie.Movie_ID = Review.Movie_ID
AND User.User_ID = "$inputQuery"
EOL;
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    $reviews = "Hasn't made a review yet.";
} else {
    $temp_array = array();
    while($x = $result->fetch_assoc()) {
        $user_link_url = "userPage.php?" . http_build_query(array('User_ID' => $x["User_ID"]));
        $title_link_url = "moviePage.php?" . http_build_query(array('Movie_ID' => $x["Movie_ID"]));
        $temp_string =
"<div class='card zdepth1'>"
. "<a href=\"$user_link_url\">"
. $x["Fname"] . " " . $x["Lname"]

. "</a>"
. " on "
. "<a href=\"$title_link_url\">"
. $x["Title"]
. "</a>"
. " - 🛁" . $x["Rating"] . "/10"
. "<br/>"
. $x["Comment"]
. "</div>";
        array_push($temp_array, $temp_string);
    }
    $reviews = implode("<br/><br/>", $temp_array);
}
$sql =
<<<EOL
SELECT Fname, Lname FROM User WHERE User_ID = "$inputQuery"
EOL;
$result = $conn->query($sql);
if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    $name = $row["Fname"] . " " . $row["Lname"];
} else {
    echo "Error! Expected 1 row. Got " . $result->num_rows . " rows.";
}
?>
<?php
echo "<h1>$name (User)</h1><br/>";
echo "<h3>Reviews:</h3><hr/>";
echo $reviews;
?>
</body>
</div>
</div>
</html>