<html>
<head>
<title>People Page</title>
<link href="style/main.css" rel="stylesheet" type="text/css">
<?php

$inputQuery = $_GET['MP_ID'];
include_once("include/dbh.inc.php");
$sql = sprintf("SELECT * FROM Movie_Person WHERE MP_ID = \"%1\$s\"",$inputQuery);
$result = $conn->query($sql);
if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    $name = $row["Fname"] . " " . $row["Mname"] . " " . $row["Lname"];
    $birth = is_null($row["Birth_Year"]) ? "unknown" : $row["Birth_Year"];
    $death = is_null($row["Death_Year"]) ? "present" : $row["Death_Year"];
} else {
    echo "Error! Expected 1 row. Got " . $result->num_rows . " rows.";
}

// Get all movies this person directed in
$sql = <<<EOD
SELECT Director.Movie_ID, Title, Year
FROM Movie_Person, Director, Movie
WHERE Movie_Person.MP_ID = Director.MP_ID AND Movie.Movie_ID = Director.Movie_ID
AND Movie_Person.MP_ID = "$inputQuery";
EOD;
$result = $conn->query($sql);
$director_array = array();
while($row  = $result->fetch_assoc()) {
    $link_url = "moviePage.php?" . http_build_query(array('Movie_ID' => $row["Movie_ID"]));
    $year_token = is_null($row["Year"]) ? "" : " (" . $row["Year"] . ")";
    $temp = "<a href=\"" . $link_url . "\">" . $row["Title"] . $year_token . "</a>";
    array_push($director_array, $temp);
}
$sql = <<<EOD
SELECT Writer.Movie_ID, Title, Year
FROM Movie_Person, Writer, Movie
WHERE Movie_Person.MP_ID = Writer.MP_ID AND Movie.Movie_ID = Writer.Movie_ID
AND Movie_Person.MP_ID = "$inputQuery";
EOD;
$result = $conn->query($sql);
$writer_array = array();
while($row  = $result->fetch_assoc()) {
    $link_url = "moviePage.php?" . http_build_query(array('Movie_ID' => $row["Movie_ID"]));
    $year_token = is_null($row["Year"]) ? "" : " (" . $row["Year"] . ")";
    $temp = "<a href=\"" . $link_url . "\">" . $row["Title"] . $year_token . "</a>";
    array_push($writer_array, $temp);
}

$sql = <<<EOD
SELECT Composer.Movie_ID, Title, Year
FROM Movie_Person, Composer, Movie
WHERE Movie_Person.MP_ID = Composer.MP_ID AND Movie.Movie_ID = Composer.Movie_ID
AND Movie_Person.MP_ID = "$inputQuery";
EOD;
$result = $conn->query($sql);
$composer_array = array();
while($row  = $result->fetch_assoc()) {
    $link_url = "moviePage.php?" . http_build_query(array('Movie_ID' => $row["Movie_ID"]));
    $year_token = is_null($row["Year"]) ? "" : " (" . $row["Year"] . ")";
    $temp = "<a href=\"" . $link_url . "\">" . $row["Title"] . $year_token . "</a>";
    array_push($composer_array, $temp);
}

$sql = <<<EOD
SELECT Producer.Movie_ID, Title, Year
FROM Movie_Person, Producer, Movie
WHERE Movie_Person.MP_ID = Producer.MP_ID AND Movie.Movie_ID = Producer.Movie_ID
AND Movie_Person.MP_ID = "$inputQuery";
EOD;
$result = $conn->query($sql);
$producer_array = array();
while($row  = $result->fetch_assoc()) {
    $link_url = "moviePage.php?" . http_build_query(array('Movie_ID' => $row["Movie_ID"]));
    $year_token = is_null($row["Year"]) ? "" : " (" . $row["Year"] . ")";
    $temp = "<a href=\"" . $link_url . "\">" . $row["Title"] . $year_token . "</a>";
    array_push($producer_array, $temp);
}

$sql = <<<EOD
SELECT Actor.Movie_ID, Title, Year, Actor.Character
FROM Movie_Person, Actor, Movie
WHERE Movie_Person.MP_ID = Actor.MP_ID AND Movie.Movie_ID = Actor.Movie_ID
AND Movie_Person.MP_ID = "$inputQuery";
EOD;
$result = $conn->query($sql);
$actor_array = array();
while($row  = $result->fetch_assoc()) {
    $link_url = "moviePage.php?" . http_build_query(array('Movie_ID' => $row["Movie_ID"]));
    $year_token = is_null($row["Year"]) ? "" : " (" . $row["Year"] . ")";
    $char_token = is_null($row["Character"]) ? "" : "\"" . $row["Character"] . "\" from ";
    $temp = "<a href=\"" . $link_url . "\">" . $char_token . $row["Title"] . $year_token . "</a>";
    array_push($actor_array, $temp);
}

$conn->close();

?>
</head>
<body>
<div id="content">
<div class="card zdepth2 expanded">
<form>
  <input type="button" value="⬅ Back" onclick="history.back()">
</form>
<h2 id="title">
<?php echo <<<EOD
$name ($birth - $death)
EOD;
?>
</h2>

<?php
if(!empty($director_array)) {
    echo "<p>Director for :</p>";
    $html_director = implode(", ",$director_array);
    echo $html_director;
}

if(!empty($writer_array)) {
    echo "<p>Writer for :</p>";
    $html_writer = implode(", ",$writer_array);
    echo $html_writer;
}
if(!empty($composer_array)) {
    echo "<p>Composer for :</p>";
    $html_composer = implode(", ",$composer_array);
    echo $html_composer;
}
if(!empty($producer_array)) {
    echo "<p>Producer for :</p>";
    $html_producer = implode(", ",$producer_array);
    echo $html_producer;
}
if(!empty($actor_array)) {
    echo "<p>Actor for :</p>";
    $html_actor = implode(", ",$actor_array);
    echo $html_actor;
}
?>
</div>
</div>
</body>
</html>