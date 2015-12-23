<?php

/* 
 * Returns a full view of a product's details given the product's ID
 */

// Access db credentials
require_once 'config.php';

// Connect to db
$connection = new mysqli($db_hostname, $db_username, $db_password, $db_database);

// If connection fails, display error message
if ($connection->connect_errno) {
    die('Unable to connect to MySQL (ERROR: ' . $connection->connect_errno . ')');
}

// Get product ID from AJAX call
$productID =  filter_input(INPUT_POST, 'product_id', FILTER_SANITIZE_STRING);

// Query the applicable product details
$query = "SELECT p.name AS p_name, " .
                "p.notes AS p_notes, " .
                "c.name AS c_name, " .
                "c.notes AS c_notes, " .
                "t.name AS t_name, " .
                "t.internal_contact AS t_iContact, " .
                "t.external_contact AS t_eContact, " .
                "t.hours AS t_hours, " .
                "t.notes AS t_notes, " .
                "supp.id AS supp_id, " .
                "supp.circumstance AS supp_circumstance, " .
                "supp.notes AS supp_notes, " .
                "s.name AS s_name, " .
                "s.internal_contact AS s_iContact, " .
                "s.external_contact AS s_eContact, " .
                "s.address AS s_address, " .
                "s.notes AS s_notes, " .
                "serv.id AS serv_id, " .
                "serv.circumstance AS serv_circumstance, " .
                "serv.notes AS serv_notes " .
         "FROM product AS p " .
         "JOIN company AS c ON p.company_id = c.id " .
         "JOIN support_assignment AS supp ON p.id = supp.product_id " .
         "JOIN technologist AS t ON supp.technologist_id = t.id " .
         "JOIN service_assignment AS serv ON p.id = serv.product_id " .
         "JOIN servicer AS s ON serv.servicer_id = s.id " .
         "WHERE p.id = $productID";

// Get result from db
$result = mysqli_query($connection, $query) or die("ERROR: mysqli_query @ psdbFullView.php/product_details");

// Store attribute values for each tuple returned from query
$tuple = 0;
while ($row = mysqli_fetch_array($result)) {
    $relation[$tuple]['p_name'] = $row['p_name'];
    $relation[$tuple]['p_notes'] = $row['p_notes'];
    $relation[$tuple]['c_name'] = $row['c_name'];
    $relation[$tuple]['c_notes'] = $row['c_notes'];
    $relation[$tuple]['t_name'] = $row['t_name'];
    $relation[$tuple]['t_iContact'] = $row['t_iContact'];
    $relation[$tuple]['t_eContact'] = $row['t_eContact'];
    $relation[$tuple]['t_hours'] = $row['t_hours'];
    $relation[$tuple]['t_notes'] = $row['t_notes'];
    $relation[$tuple]['supp_id'] = $row['supp_id'];
    $relation[$tuple]['supp_circumstance'] = $row['supp_circumstance'];
    $relation[$tuple]['supp_notes'] = $row['supp_notes'];
    $relation[$tuple]['s_name'] = $row['s_name'];
    $relation[$tuple]['s_iContact'] = $row['s_iContact'];
    $relation[$tuple]['s_eContact'] = $row['s_eContact'];
    $relation[$tuple]['s_address'] = $row['s_address'];
    $relation[$tuple]['s_notes'] = $row['s_notes'];
    $relation[$tuple]['serv_id'] = $row['serv_id'];
    $relation[$tuple]['serv_circumstance'] = $row['serv_circumstance'];
    $relation[$tuple]['serv_notes'] = $row['serv_notes'];
    $tuple++;
}

// Get number of tuples in relation
$numTuples = count($relation);

// Display section heading
echo "<h2>Product Details</h2>";

// Display product details
echo "<table border='1px'>"
        . "<tr><td class='heading'>Name</td><td>" . $relation[0]['p_name'] . "</td></tr>"
        . "<tr><td class='heading'>Company</td><td>" . $relation[0]['c_name'] . "</td></tr>"
        . "<tr><td class='heading'>Notes</td><td class='notes'><ul>" . $relation[0]['p_notes']. $relation[0]['c_notes'] . "</ul></td></tr>"
   . "</table><br>";

// Section separator
echo "<br>";

// Display section heading
echo "<h2>Technologist Details</h2>";

// Track used suppport assignment IDs
$suppAssignIDs;

// Display technical support details for each support assignment
for ($i = 0; $i < $numTuples; $i++) {
    // If support assignment ID is not listed in array of used IDs...
    if (!in_array($relation[$i]['supp_id'], $suppAssignIDs)) {
        // Display technical support details
        echo "<p><span class='heading'>Condition:</span>  " . $relation[$i]['supp_circumstance'] . "</p>";
        echo $relation[$i]['supp_notes'];
        echo "<table border='1px'>"
                . "<tr><td class='heading'>Technologist</td><td>" . $relation[$i]['t_name'] . "</td></tr>"
                . "<tr><td class='heading'>Internal Contact</td><td>" . $relation[$i]['t_iContact'] . "</td></tr>"
                . "<tr><td class='heading'>External Contact</td><td>" . $relation[$i]['t_eContact'] . "</td></tr>"
                . "<tr><td class='heading'>Hours</td><td>" . $relation[$i]['t_hours'] . "</td></tr>"
                . "<tr><td class='heading'>Notes</td><td class='notes'><ul>" . $relation[$i]['t_notes'] . "</ul></td></tr>"
           . "</table><br>";
        // Add support assignment ID to list of used IDs
        $suppAssignIDs[] = $relation[$i]['supp_id'];
    }
}

// Section separator
echo "<br>";

// Display section heading
echo "<h2>Servicer Details</h2>";

// Track used service assignment IDs
$servAssignIDs;

// Display repair service details for each service assignment
for ($i = 0; $i < $numTuples; $i++) {
    // If service assignment ID is not listed in array of used IDs...
    if (!in_array($relation[$i]['serv_id'], $servAssignIDs)) {
        // Display repair service details
        echo "<p><span class='heading'>Condition:</span>  " . $relation[$i]['serv_circumstance'] . "</p>";
        echo $relation[$i]['serv_notes'];
        echo "<table border='1px'>"
                . "<tr><td class='heading'>Servicer</td><td>" . $relation[$i]['s_name'] . "</td></tr>"
                . "<tr><td class='heading'>Internal Contact</td><td>" . $relation[$i]['s_iContact'] . "</td></tr>"
                . "<tr><td class='heading'>External Contact</td><td>" . $relation[$i]['s_eContact'] . "</td></tr>"
                . "<tr><td class='heading'>Facility Address</td><td>" . $relation[$i]['s_address'] . "</td></tr>"
                . "<tr><td class='heading'>Notes</td><td class='notes'><ul>" . $relation[$i]['s_notes'] . "</ul></td></tr>"
           . "</table><br>";
        // Add service assignment ID to list of used IDs
        $servAssignIDs[] = $relation[$i]['serv_id'];
    }
}

// Release result data
mysqli_free_result($result);

// Close db connection
mysqli_close($connection);