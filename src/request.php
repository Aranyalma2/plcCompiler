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
        $project_name = $data['project']['name'] . "-" . md5(rand());;
        $author = md5($data['project']['author']);
        $library_id = $data['project']['libraryID'];

        // Accessing block and module information
        $blocks = $data['blocks'];
        $modules = $data['modules'];

        //Make user dir
        if (!file_exists($translatedModelsPATH . $author)) {
            // Create the directory
            if (!mkdir($translatedModelsPATH . $author, 0777, true)) {
                echo 'ERROR: Failed to create user directory.';
                return;
            }
        }

        //translate project and save it
        $output = translateModel($library_id, $blocks, $modules);
        $project_translatedPath = $translatedModelsPATH . $author . '/' . $project_name;
        if (mkdir($project_translatedPath, 0777, true)) {
            file_put_contents($project_translatedPath . "/" . $project_name . ".ino", $output);
        } else {
            echo 'ERROR: Failed to create a project directory. (ino)';
            return;
        }

        //Compile project with arduino cli and save it
        $FQDN = "arduino:avr:nano:cpu=atmega328";
        echo shell_exec("arduino-cli --config-file arduino/config.yaml compile --fqbn " . $FQDN . " " . $project_translatedPath . " -e");

        echo $project_compiledPath = $compiledBinariesPATH . $author . '/' . $project_name;
        if (mkdir($project_compiledPath, 0777, true)) {
            $builtProjectPath = $project_translatedPath . "/build";
            zipBinary($builtProjectPath, $project_compiledPath);
        } else {
            echo 'ERROR: Failed to create a project directory. (binary)';
            return;
        }


        //Printing the project name
        echo "Project Name: $project_name <br><br>";
        echo "Project compiled, download: <br><br>";

        //Download button
        echo `<button onclick="location.href=' ` . $project_compiledPath . "" . `'">Project zip</button>`;

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

function zipBinary($buildPath, $exportpath)
{

    $rootPath = realpath($buildPath);

    // Initialize archive object
    $zip = new ZipArchive();
    $zip->open($exportpath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

    // Create recursive directory iterator
    /** @var SplFileInfo[] $files */
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($rootPath),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($files as $name => $file) {
        // Skip directories (they would be added automatically)
        if (!$file->isDir()) {
            // Get real and relative path for current file
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($rootPath) + 1);

            // Add current file to archive
            $zip->addFile($filePath, $relativePath);
        }
    }

    // Zip archive will be created only after closing object
    $zip->close();
}
