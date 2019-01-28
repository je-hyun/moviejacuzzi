<html>
    <head>
        <title>Query Results</title>
        <link href="style/main.css" rel="stylesheet" type="text/css">
    </head>
<body>
<div id="content">
<div class="card zdepth2 expanded">
<form>
  <input type="button" value="⬅ Back" onclick="history.back()">
</form>

<?php
include_once("include/dbh.inc.php");

### Function Definitions Here

function get_genres($movie_id, $conn) {
    $sql_get_genres =
<<<END
SELECT Genre.Genre_Name
FROM Movie INNER JOIN Genre
ON Movie.Movie_ID = Genre.Movie_ID
WHERE Movie.Movie_ID = "$movie_id"
END;
    $my_query = $conn->query($sql_get_genres);
    $genre_list = array();
    while($g = $my_query->fetch_assoc()) {
        array_push($genre_list,$g["Genre_Name"]);    
    }
    return $genre_list;
}

function get_movies($query, $conn) {
    $sql = "SELECT Movie.Movie_ID, Movie.Title, Movie.Year, Movie.Minutes FROM Movie\n"
    . "WHERE Movie.Title = \"" . $query . "\" AND Movie.Minutes > 60\n"
    . "LIMIT 10";
    return $conn->query($sql);
}

function get_people($query, $conn) {
    $query_array = explode(" ",$query);
    $word_count = count($query_array);
    if($word_count == 0) {
        $sql = "";
    } else if($word_count == 1) {
        $sql = sprintf("SELECT Movie_Person.MP_ID,Movie_Person.Fname,Movie_Person.Mname,Movie_Person.Lname FROM Movie_Person WHERE Movie_Person.Fname=\"%1\$s\" OR Movie_Person.Lname=\"%1\$s\" OR Movie_Person.Mname=\"%1\$s\"\n"
        . "LIMIT 10", $query);
    } else if ($word_count == 2) {
        $sql = sprintf("SELECT Movie_Person.MP_ID,Movie_Person.Fname,Movie_Person.Mname,Movie_Person.Lname FROM Movie_Person WHERE Movie_Person.Fname=\"%s\" AND Movie_Person.Lname=\"%s\"\n"
        . "LIMIT 10", $query_array[0], $query_array[1]);
    } else if ($word_count > 2) {
        // Create string from all values in the query_array besides first and last.
        // This string will be used as the middle name.
        $Mname = "";
        for ($x = 0; $x < count($query_array)-2; $x++) {
            $Mname .= $query_array[$x+1];
        }
        $sql = sprintf("SELECT Movie_Person.MP_ID,Movie_Person.Fname,Movie_Person.Mname,Movie_Person.Lname FROM Movie_Person WHERE Movie_Person.Fname=\"%s\" AND Movie_Person.Mname = \"%s\" AND Movie_Person.Lname=\"%s\"\n"
        . "LIMIT 10", $query_array[0] ,$Mname ,array_values(array_slice($query_array, -1))[0]);
    }
     
    return $conn->query($sql);
}

function get_people_preview($query, $conn) {
    $sql =
<<<EOD
(SELECT Movie_Person.MP_ID,Movie_Person.Fname,Movie_Person.Mname,Movie_Person.Lname,"Actor" AS "Role",Movie.Title,Actor.Character
FROM Movie_Person
INNER JOIN Actor
	ON Actor.MP_ID = Movie_Person.MP_ID
INNER JOIN Movie
	ON Movie.Movie_ID = Actor.Movie_ID
WHERE Movie_Person.MP_ID = "$query"
ORDER BY Movie.Year DESC
LIMIT 1)
UNION
(SELECT Movie_Person.MP_ID,Movie_Person.Fname,Movie_Person.Mname,Movie_Person.Lname,"Director",Movie.Title,NULL
 FROM Movie_Person
INNER JOIN Director
	ON Director.MP_ID = Movie_Person.MP_ID
INNER JOIN Movie
	ON Movie.Movie_ID = Director.Movie_ID
WHERE Movie_Person.MP_ID = "$query"
ORDER BY Movie.Year DESC
LIMIT 1)
UNION
(SELECT Movie_Person.MP_ID,Movie_Person.Fname,Movie_Person.Mname,Movie_Person.Lname,"Writer",Movie.Title,NULL
 FROM Movie_Person
INNER JOIN Writer
	ON Writer.MP_ID = Movie_Person.MP_ID
INNER JOIN Movie
	ON Movie.Movie_ID = Writer.Movie_ID
WHERE Movie_Person.MP_ID = "$query"
ORDER BY Movie.Year DESC
LIMIT 1)
UNION
(SELECT Movie_Person.MP_ID,Movie_Person.Fname,Movie_Person.Mname,Movie_Person.Lname,"Producer",Movie.Title,NULL
 FROM Movie_Person
INNER JOIN Producer
	ON Producer.MP_ID = Movie_Person.MP_ID
INNER JOIN Movie
	ON Movie.Movie_ID = Producer.Movie_ID
WHERE Movie_Person.MP_ID = "$query"
ORDER BY Movie.Year DESC
LIMIT 1)
UNION
(SELECT Movie_Person.MP_ID,Movie_Person.Fname,Movie_Person.Mname,Movie_Person.Lname,"Composer",Movie.Title,NULL
 FROM Movie_Person
INNER JOIN Composer
	ON Composer.MP_ID = Movie_Person.MP_ID
INNER JOIN Movie
	ON Movie.Movie_ID = Composer.Movie_ID
WHERE Movie_Person.MP_ID = "$query"
ORDER BY Movie.Year DESC
LIMIT 1)
EOD;
    $my_query = $conn->query($sql);
    // array of dictionaries
    $preview_list = array();
    while($x = $my_query->fetch_assoc()) {
        array_push($preview_list, $x);
    }
    return $preview_list;
}

#### Main Code Here:

$query = $_GET['inputQuery'];
$movie_result = get_movies($query, $conn);

### This section is for printing the list of titles
if ($movie_result->num_rows > 0) {
    echo "<h3>Titles</h3>";
    echo "<ol>";
    // Create HTML from list of rows
    while($row = $movie_result->fetch_assoc()) { 
        
        $link_url = "moviePage.php?" . http_build_query(array('Movie_ID' => $row["Movie_ID"]));
        $new_html = sprintf("<li><a href=%s> %s (%s)</a>",
            $link_url, $row["Title"], $row["Year"]);
        $list_genres = get_genres($row["Movie_ID"], $conn);
        if (!empty($list_genres)) {
            $new_html .= "<EM> (";
        
            for ($i = 0; $i < count($list_genres); $i++) {
                $new_html .= $list_genres[$i];
                if ($i < count($list_genres) - 1) {
                    $new_html .= ", ";
                }
            }
            $new_html .= ")</EM>";
        }
        $new_html .= "</li>";
        echo $new_html;
    }
    echo "</ol>";
} else {
    echo "<EM>Found no title like \"" . $query . "\"</EM>";
}
### This section is for printing list of people

$people_result=get_people($query, $conn);
if ($people_result->num_rows > 0) {
    echo "<h3>People</h3>";
    echo "<ol>";
    // Create HTML from list of rows
    while($row = $people_result->fetch_assoc()) { 
        $link_url = "peoplePage.php?" . http_build_query(array('MP_ID' => $row["MP_ID"]));
        $new_html = sprintf("<li><a href=%s>%s %s %s</a>",
            $link_url, $row["Fname"], $row["Mname"], $row["Lname"]);
        
        $new_html .= "<EM> ";
        $list_pp = get_people_preview($row["MP_ID"], $conn);
        for ($i = 0; $i < count($list_pp); $i++) {
            if ($list_pp[$i]["Role"] == "Actor") {
                $temp_character = $list_pp[$i]["Character"] == "" ? "" : "(" . $list_pp[$i]["Character"] . ")";
                $new_html .= <<<EOD
{$list_pp[$i]["Role"]} {$temp_character} in {$list_pp[$i]["Title"]}
EOD;
            } else {
                $new_html .= <<<EOD
{$list_pp[$i]["Role"]} of {$list_pp[$i]["Title"]}
EOD;
            }            

            if ($i < count($list_pp) - 1) {
                $new_html .= ", ";
            }
        }
        $new_html .= "</EM></li>";
        echo $new_html;
    }
    echo "</ol>";
} else {
    echo "<EM>Found no person like \"" . $query . "\"</EM>";
}

$conn->close();
?>
</div>
</div>
</body>
</html>


