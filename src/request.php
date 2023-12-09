<?php

include("CodeMakerCore.php");

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
        $blocks = $data['blocks'];
        
        // Accessing module information
        $modules = $data['modules'];
        
        // Process the data as needed
        // You can perform various actions with the extracted data
        
        // For example, printing the project name
        echo "Project Name: $project_name <br><br>";
        echo "Project compiled, download: <br><br>";

        echo "<button>Project zip</button>";

        $output = translateModel($library_id, $blocks, $modules);
        
        echo $output;

        $str=rand();
        $result = md5($str);

        file_put_contents("outputs/perf_test/output-".$result.".txt", $output);

        /*

        $mockdata = json_decode(file_get_contents("libraryExtracts/" . $library_id. ".json"), true);
        $mocktemplate = json_decode(file_get_contents("templates/blockFunctionTemplate.json"), true);
        
        foreach (createModuleConstructors($modules, $mockdata["modules"]) as $key => $value) {
            echo"". $key ." ->  ". $value ."<br>";
        }

        
        foreach (createBlockConstructors($blocks, $mockdata["blocks"]) as $key => $value) {
            echo"". $key ." ->  ". $value ."<br>";
        }

        foreach (createConnections($blocks, $mocktemplate) as $key => $value) {
            echo"". $key ." ->  ". $value ."<br>";
        }

        foreach (schedulers($blocks, $modules, $mockdata["blocks"], $mocktemplate['run']) as $key => $value) {
            echo"". $key ." ->  ". $value ."<br>";
        }
        */

        

        
    } else {
        // JSON decoding failed
        echo "Error decoding JSON data.\n";
    }
} else {
    // Request method is not POST
    echo "Invalid request method. Use POST.\n";
}
?>