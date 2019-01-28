<html>
<meta charset="UTF-16">
<head>
    <title>Movie Page</title>
    <link href="style/main.css" rel="stylesheet" type="text/css">
</head>
<body>
<div id="content">
<?php
// Get variables necessary for page
$inputQuery = $_GET['Movie_ID'];
include_once("include/dbh.inc.php");

// Handle comment submission
$new_comment = $_POST['comment'];
$new_comment_user = $_POST['user'];
$new_rating = $_POST['rating'];
if (!is_null($new_comment)) {
    $sql =
<<<EOL
INSERT INTO Review
VALUES("$new_comment_user", "$inputQuery", "$new_rating", "$new_comment");
EOL;
    $result = $conn->query($sql);
}

// Get basics: row, title, year, minutes
$sql = "SELECT `Title`,`Year`,`Minutes` FROM Movie WHERE Movie_ID = \"" . $inputQuery . "\"";
$result = $conn->query($sql);
if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    $title = $row["Title"];
    $year = $row["Year"];
    $minutes = $row["Minutes"];
    $minutes = floor((int)($minutes) / 60) . "h" .  ((int)($minutes) % 60) . "m";
} else {
    echo "Error! Expected 1 row. Got " . $result->num_rows . " rows.";
}
// Get Average Rating
$sql = <<<EOL
SELECT AVG(Review.Rating) AS `Score` FROM `Review` WHERE Review.Movie_ID = "$inputQuery";
EOL;
$result = $conn->query($sql);
if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    if (is_null($row["Score"])) {
        $score = "?";
    } else {
        $score = round($row["Score"],1);
    }
    
} else {
    echo "Error! Expected 1 row. Got " . $result->num_rows . " rows.";
}

// Get Genre List
$sql = 
<<<EOL
SELECT Genre_Name FROM Genre WHERE Movie_ID = "$inputQuery";
EOL;
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    $genres = "[no genre information]";
} else {
    $genre_array = array();
    while($x = $result->fetch_assoc()) {
        array_push($genre_array, $x["Genre_Name"]);
    }
    $genres = implode(", ", $genre_array);
}

// Get Directors List
$sql = <<<EOL
SELECT Movie_Person.MP_ID, Movie_Person.Fname, Movie_Person.Mname, Movie_Person.Lname
FROM Movie, Director, Movie_Person
WHERE Movie.Movie_ID = Director.Movie_ID AND Movie_Person.MP_ID = Director.MP_ID
AND Movie.Movie_ID = "$inputQuery";
EOL;
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    $directors = "[no director information]";
} else {
    $temp_array = array();
    while($x = $result->fetch_assoc()) {
        $link_url = "peoplePage.php?" . http_build_query(array('MP_ID' => $x["MP_ID"]));
        $temp_string = "<a href=\"$link_url\">" . $x["Fname"] . " " . $x["Mname"] . " " . $x["Lname"] . "</a>";
        array_push($temp_array, $temp_string);
    }
    $directors = implode(", ", $temp_array);
}

// Get Writers List
$sql = <<<EOL
SELECT Movie_Person.MP_ID, Movie_Person.Fname, Movie_Person.Mname, Movie_Person.Lname
FROM Movie, Writer, Movie_Person
WHERE Movie.Movie_ID = Writer.Movie_ID AND Movie_Person.MP_ID = Writer.MP_ID
AND Movie.Movie_ID = "$inputQuery";
EOL;
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    $writers = "[no writers information]";
} else {
    $temp_array = array();
    while($x = $result->fetch_assoc()) {
        $link_url = "peoplePage.php?" . http_build_query(array('MP_ID' => $x["MP_ID"]));
        $temp_string = "<a href=\"$link_url\">" . $x["Fname"] . " " . $x["Mname"] . " " . $x["Lname"] . "</a>";
        array_push($temp_array, $temp_string);
    }
    $writers = implode(", ", $temp_array);
}

// Get Composers List
$sql = <<<EOL
SELECT Movie_Person.MP_ID, Movie_Person.Fname, Movie_Person.Mname, Movie_Person.Lname
FROM Movie, Composer, Movie_Person
WHERE Movie.Movie_ID = Composer.Movie_ID AND Movie_Person.MP_ID = Composer.MP_ID
AND Movie.Movie_ID = "$inputQuery";
EOL;
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    $composers = "[no composers information]";
} else {
    $temp_array = array();
    while($x = $result->fetch_assoc()) {
        $link_url = "peoplePage.php?" . http_build_query(array('MP_ID' => $x["MP_ID"]));
        $temp_string = "<a href=\"$link_url\">" . $x["Fname"] . " " . $x["Mname"] . " " . $x["Lname"] . "</a>";
        array_push($temp_array, $temp_string);
    }
    $composers = implode(", ", $temp_array);
}

// Get Producers List
$sql = <<<EOL
SELECT Movie_Person.MP_ID, Movie_Person.Fname, Movie_Person.Mname, Movie_Person.Lname
FROM Movie, Producer, Movie_Person
WHERE Movie.Movie_ID = Producer.Movie_ID AND Movie_Person.MP_ID = Producer.MP_ID
AND Movie.Movie_ID = "$inputQuery";
EOL;
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    $producers = "[no producers information]";
} else {
    $temp_array = array();
    while($x = $result->fetch_assoc()) {
        $link_url = "peoplePage.php?" . http_build_query(array('MP_ID' => $x["MP_ID"]));
        $temp_string = "<a href=\"$link_url\">" . $x["Fname"] . " " . $x["Mname"] . " " . $x["Lname"] . "</a>";
        array_push($temp_array, $temp_string);
    }
    $producers = implode(", ", $temp_array);
}

// Get Actors List
$sql = <<<EOL
SELECT Movie_Person.MP_ID, Movie_Person.Fname, Movie_Person.Mname, Movie_Person.Lname, Actor.Character
FROM Movie, Actor, Movie_Person
WHERE Movie.Movie_ID = Actor.Movie_ID AND Movie_Person.MP_ID = Actor.MP_ID
AND Movie.Movie_ID = "$inputQuery";
EOL;
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    $actors = "[no actors information]";
} else {
    $temp_array = array();
    while($x = $result->fetch_assoc()) {
        $link_url = "peoplePage.php?" . http_build_query(array('MP_ID' => $x["MP_ID"]));
        $char_token = is_null($x["Character"]) ? "" : " as \""  . $x["Character"] . "\"";
        $temp_string = "<a href=\"$link_url\">" . $x["Fname"] . " " . $x["Mname"] . " " . $x["Lname"] . $char_token . "</a>";
        array_push($temp_array, $temp_string);
    }
    $actors = implode(", ", $temp_array);
}

// List all user reviews
$sql = <<<EOL
SELECT `Comment`,`Rating`,Review.`User_ID`,`Fname`,`Lname`
FROM `Review`, `User`
WHERE Review.User_ID = User.User_ID
AND Review.Movie_ID="$inputQuery"
EOL;
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    $reviews = "No reviews. Be the first to leave a review!";
} else {
    $temp_array = array();
    while($x = $result->fetch_assoc()) {
        $link_url = "userPage.php?" . http_build_query(array('User_ID' => $x["User_ID"]));
        $temp_string =
"<div class = \"card zdepth1\">"
. "<a href=\"$link_url\">"
. $x["Fname"] . " " . $x["Lname"]
. "</a>"
. " - 🛁" . $x["Rating"] . "/10"
. "<br/>"
. $x["Comment"]
. "</div>";
        array_push($temp_array, $temp_string);
    }
    $reviews = implode("", $temp_array);
}

// Get Average Rating

$conn->close();


// Render to webpage
echo <<<EOL
<div class="card zdepth2 expanded">
<form>
  <input type="button" value="⬅ Back" onclick="history.back()">
</form>
<h1 id="title" style="display:inline;">$title ($year) </h1> <h3 style="display:inline;">🛁$score/10</h3>
<hr/>
<p class="title-description">$minutes | $genres</p>
<p>Director(s): $directors</p>
<p>Writer(s): $writers</p>
<p>Composer(s): $composers</p>
<p>Producer(s): $producers</p>
<p>Actor(s): $actors</p>
EOL;

?>
<h3>User Reviews</h3><hr/>
<table id="comment_table">
<?php
echo $reviews;
?>
</table>
<br/><br/>
<h3>Leave a review</h3><hr/>
<form action="" method="post">
    <table border="0" cellspacing="0" cellpadding="0" id="form-table">
    <tr>
    <td>
    <select name="user">
            <option value="u00007">Je Hyun Kim</option>
            <option value="u00006">Nav Singh</option>
            <option value="u00008">Andre Hood</option>
            <option value="u00000">Nikhil Yadav</option>
    </select>
    🛁
    <select name="rating">
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
        <option value="6">6</option>
        <option value="7">7</option>
        <option value="8">8</option>
        <option value="9">9</option>
        <option value="10">10</option>
    </select>
    / 10
    </td>
    </tr>
    <tr>
        <td><textarea name="comment" rows="5" cols="50"></textarea></td>
    </tr>
    <tr>
    <td colspan="2"  id="buttons">
        <br/>
        <input type="submit" value="Submit Review"/>
    </td>
    </tr>
    </table>
</form>
</div>
</div>
</body>
</html>