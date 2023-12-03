<?php

include("translator.php");

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Get the JSON data from the request body
    $json_data = file_get_contents('php://input');
    
    // Decode the JSON data
    $data = json_decode($json_data, true);
    
    // Check if the JSON decoding was successful
    if ($data !== null) {
        
        // Accessing project information
        $project_name = $data['project']['name'];
        $author = $data['project']['author'];
        $board_id = $data['project']['boardID'];
        $library_id = $data['project']['libraryID'];
        
        // Accessing block information
        $io_blocks = $data['blocks']['IO'];
        $non_io_blocks = $data['blocks']['NonIO'];
        
        // Accessing module information
        $modules = $data['modules'];
        
        // Process the data as needed
        // You can perform various actions with the extracted data
        
        // For example, printing the project name
        echo "Project Name: $project_name\n";

        translateModel($library_id, $io_blocks, $non_io_blocks, $modules);

        createModulesConstructors($modules, $modules);

        

        
    } else {
        // JSON decoding failed
        echo "Error decoding JSON data.\n";
    }
} else {
    // Request method is not POST
    echo "Invalid request method. Use POST.\n";
}
?>