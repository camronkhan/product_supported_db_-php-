<?php

/* 
 * Returns a quick view of all possible matches given the user's input
 */

// Access db credentials
require_once 'config.php';

// Connect to db
$connection = new mysqli($db_hostname, $db_username, $db_password, $db_database);

// If connection fails, display error message
if ($connection->connect_errno) {
    die('Unable to connect to MySQL (ERROR: ' . $connection->connect_errno . ')');
}

// Get search value from AJAX call
$userInput =  filter_input(INPUT_POST, 'user_input', FILTER_SANITIZE_STRING);

// If user provides no search value, return all records
if ($userInput === '') {
    $query = "SELECT p.id AS p_id, p.name AS p_name, c.name AS c_name " .
             "FROM product AS p, company AS c " .
             "WHERE p.company_id = c.id " .
             "ORDER BY p.name";
}
// Otherwise, search based on criteria provided
else {
    $query = "SELECT p.id AS p_id, p.name AS p_name, c.name AS c_name " .
             "FROM product AS p, company AS c " .
             "WHERE p.company_id = c.id " .
             "AND (p.search_term LIKE '%$userInput%')" .
             "ORDER BY p.name";
}

// Get result from db
$result = mysqli_query($connection, $query) or die("ERROR: mysqli_query @ psdbQuickView.php");

// Display section heading
echo "<h2>Results</h2>";

// Get number of rows returned
$numRecords = mysqli_num_rows($result);
echo "Displaying $numRecords record(s)<br><br>";

// If any records are returned, display them
if ($numRecords > 0) {
    echo "<table border='1px'><tr class='heading'><td>Product</td><td class='heading'>Company</td></tr>";
    while ($row = mysqli_fetch_array($result)) {
        echo "<tr><td><a href='#' class='prodID' onclick='return getFullView(" . $row['p_id'] . ")'>" . $row['p_name'] . "</a></td><td>" . $row['c_name'] . "</td></tr>"; 
    }
    echo "</table>";
}

// Release result data
mysqli_free_result($result);

// Close db connection
mysqli_close($connection);