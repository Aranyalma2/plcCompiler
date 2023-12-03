<?php
function translateModel($library_id, $blocks, $modules){

//--------------LOAD LIBRARY AND TEMPLATES--------------
$libraryExtract = json_decode(file_get_contents("libraryExtracts/" . $library_id. ".json"), true);

$projectTemplate = file_get_contents("templates/projectTemplate.txt");

$blockFunctionTemplates = json_decode(file_get_contents("templates/blockFunctionTemplate.json"), true);

//-------------CREATED MODEL ELEMENTS CONTAINERS-------------

$CODE_CONTAINTER_CONSTRUCTORS = array();


}

function createModuleConstructors($modulesModel, $modulesLibrary){
    $moduleConstructors = array();

    foreach ($modulesModel as $key => $moduleProperties) {
        $moduleConstructor = $modulesLibrary[$moduleProperties['classID']]['constructor'];

        $valuesById = [];
        foreach ($moduleProperties['parameterlist'] as $id => $value) {
            $valuesById[$id] = $value;
        }
        $resultString = implode(',', $valuesById);
        
        $moduleConstructor = str_replace("%NAME%", getModuleName($moduleProperties['moduleID']), $moduleConstructor);
        $moduleConstructors[$key] = str_replace("%PARAMETERLIST%", $resultString, $moduleConstructor);
        
    }  
    return $moduleConstructors;
        
}

function getModuleName($key){
    return "m".$key;
}

function createBlockConstructors($blocksModel, $blocksLibrary){
    $blockConstructors = array();
    foreach ($blocksModel as $typeKey => $blockTypes) {
        foreach ($blockTypes as $key => $blockProperties) {
            $blockConstructor = $blocksLibrary[$typeKey][$blockProperties['blockID']]['constructor'];

            $blockConstructor = str_replace("%NAME%", getModuleName($blockProperties['blockID']), $blockConstructor);
            $blockConstructors[$typeKey.$key] = str_replace("%UNIQUE_ID%", $blockProperties['blockID'], $blockConstructor);
            
        }
    }   
    return $blockConstructors;       
}

function getBlockName($key){
    return "b".$key;
}



?>