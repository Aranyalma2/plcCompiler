<?php

include("CodeMakerCore.php");

$translatedModelsPATH = "outputs/translated/";
$compiledBinariesPATH = "outputs/compiled/";

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Get the JSON data from the request body
    $json_data = file_get_contents('php://input');
    
    // Decode the JSON data
    $data = json_decode($json_data, true);
    
    // Check if the JSON decoding was successful
    if ($data !== null) {
        
        // Accessing project information
        $project_name = $data['project']['name'] ."-". md5(rand());;
        $author = md5($data['project']['author']);
        $library_id = $data['project']['libraryID'];
        
        // Accessing block and module information
        $blocks = $data['blocks'];
        $modules = $data['modules'];

        //Make user dir
        if (!file_exists($translatedModelsPATH . $author)) {
        // Create the directory
            /*if (!mkdir($translatedModelsPATH . $author), 0777, true) {
                echo 'ERROR: Failed to create user directory.';
                return;
            }*/
            if (!mkdir($translatedModelsPATH . $author)) {
                echo 'ERROR: Failed to create user directory.';
                return;
            }              
        }   

        //Printing the project name
        echo "Project Name: $project_name <br><br>";
        echo "Project compiled, download: <br><br>";
        
        //Download button
        echo "<button>Project zip</button>";

        //$output = translateModel($library_id, $blocks, $modules);
        
        //echo $output;

        //$str=rand();
        //$result = md5(rand());

        //file_put_contents("outputs/perf_test/output-".$result.".txt", $output);


        

        
    } else {
        // JSON decoding failed
        echo "Error decoding JSON data.\n";
    }
} else {
    // Request method is not POST
    echo "Invalid request method. Use POST.\n";
}
?>