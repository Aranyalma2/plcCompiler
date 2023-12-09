<?php

include("codeBuilder.php");


function translateModel($library_id, $blocksModel, $modulesModel){

//--------------LOAD LIBRARY AND TEMPLATES--------------
$libraryExtract = json_decode(file_get_contents("libraryDatas/libraryExtracts/" . $library_id. ".json"), true);

$projectTemplate = file_get_contents("libraryDatas/projectTemplate.txt");

$blockFunctionTemplates = $libraryExtract['functiontemplates'];

//-------------CREATED MODEL ELEMENTS CONTAINERS-------------

$CODE_CONTAINTER_CONSTRUCTORS = array();
$CODE_CONTAINTER_CONNECTIONS = array();
$CODE_CONTAINTER_RUN = array();


//-------------------------RUN-------------------------
$CODE_CONTAINTER_CONSTRUCTORS += createModuleConstructors($modulesModel, $libraryExtract['modules']);
$CODE_CONTAINTER_CONSTRUCTORS += createBlockConstructors($blocksModel, $libraryExtract['blocks']);
$CODE_CONTAINTER_CONNECTIONS += createConnections($blocksModel, $blockFunctionTemplates['setInput']);
$CODE_CONTAINTER_RUN += schedulers($blocksModel, $modulesModel, $libraryExtract["blocks"], $blockFunctionTemplates['run']);

return fillProjectTemplate($libraryExtract['include'], $CODE_CONTAINTER_CONSTRUCTORS, $CODE_CONTAINTER_CONNECTIONS, $CODE_CONTAINTER_RUN, $projectTemplate);

}



function fillProjectTemplate($include, $constructors, $connections, $runqueue, $templateProject){
    
    $templateProject = str_replace("%INCLUDES%", $include, $templateProject);
    $templateProject = str_replace("%CONSTRUCTORS%", implode("\n", $constructors), $templateProject);
    $templateProject = str_replace("%CONNECTIONS%", implode("\n", $connections), $templateProject);
    $templateProject = str_replace("%RUNTIME%", implode("\n", $runqueue), $templateProject);

    return $templateProject;

}


?>