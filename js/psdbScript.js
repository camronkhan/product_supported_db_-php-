/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function getQuickView() {
    // Get user input
    var userInput = $('#user-input').val();
    
    // Remove whitespace and special characters, then convert to lowercase
    userInput = produceSearchTerm(userInput);

    // Send data to search.php
    $.ajax({
        method: "POST",
        url: "psdbQuickView.php",
        data: {
            user_input: userInput
        },
        success: function(data) {
            $('#output').html(data);
        },
        error: function() {
            alert('ERROR: AJAX request failed (getQuickView)');
        }
    });
    
    // Return false to cancel GET command
    return false;
}

function getFullView(productID) {
    // Get product ID
    var pid = productID;
    
    // Send data to search.php
    $.ajax({
        method: "POST",
        url: "psdbFullView.php",
        data: {
            product_id: pid
        },
        success: function(data) {
            $('#output').html(data);
        },
        error: function() {
            alert('ERROR: AJAX request failed (getFullView)');
        }
    });
    
    // Return false to cancel GET command
    return false;
}

// Regular expressions to filter out special characters / whitespace and convert to lower case
// NOTE: Will not filter out the following characters: `^_[]\
function produceSearchTerm (input) {
    // Remove special charaters
    var temp = input.replace(/[^A-z0-9]/gi, '');
    
    // Remove whitespace
    temp = temp.replace(/[ ]/gi, '');

    // Lowercase
    temp = temp.toLowerCase();

    // Return string
    return temp;
}