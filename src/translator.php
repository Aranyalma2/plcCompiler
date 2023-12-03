<?php
function translateModel($library_id, $io_blocks, $non_io_blocks, $modules){

//--------------LOAD LIBRARY AND TEMPLATES--------------
$libraryExtract = json_decode(file_get_contents("libraryExtracts/" . $library_id .".json"), true);

$projectTemplate = file_get_contents("templates/projectTemplate.txt");

$blockFunctionTemplates = json_decode(file_get_contents("templates/blockFunctionTemplates.json"));

//-------------CREATED MODEL ELEMENTS CONTAINERS-------------

$CODE_CONTAINTER_CONSTRUCTORS = array();


}

function createModulesConstructors($modulesModel, $modulesLibrary){
    $moduleConstructors = array();

    foreach ($modulesModel as $key => $value) {
        echo "". $key ."". $value ."";
    }
}

?>