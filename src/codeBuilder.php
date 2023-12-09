<?php function createModuleConstructors($modulesModel, $modulesLibrary)
{
    $moduleConstructors = array();
    foreach ($modulesModel as $key => $moduleProperties)
    {
        $moduleConstructor = $modulesLibrary[$moduleProperties["classID"]]["constructor"];

        $valuesById = [];
        foreach ($moduleProperties["parameterlist"] as $id => $value)
        {
            $valuesById[$id] = $value;
        }
        $resultString = implode(",", $valuesById);

        $moduleConstructor = str_replace("%NAME%", getModuleName($moduleProperties["moduleID"]) , $moduleConstructor);
        $moduleConstructors[getModuleName($moduleProperties["moduleID"]) ] = str_replace("%PARAMETERLIST%", $resultString, $moduleConstructor);
    }
    return $moduleConstructors;
}

function getModuleName($key)
{
    return "m" . $key;
}

function createBlockConstructors($blocksModel, $blocksLibrary)
{
    $blockConstructors = array();
    foreach ($blocksModel as $key => $blockProperties)
    {
        $blockConstructor = $blocksLibrary[$blockProperties["classID"]]["constructor"];

        $blockConstructor = str_replace("%NAME%", getblockName($blockProperties["blockID"]) , $blockConstructor);
        $blockConstructors[getblockName($blockProperties["blockID"]) ] = str_replace("%UNIQUE_ID%", $blockProperties["blockID"], $blockConstructor);
    }
    return $blockConstructors;
}

function getBlockName($key)
{
    return "b" . $key;
}

function createConnections($blocksModel, $functionsTemplate)
{
    $connectionList = array();

    $setInputFromBlock_Template = $functionsTemplate["block"];
    $setInputFromConstans_Template = $functionsTemplate["constans"];

    foreach ($blocksModel as $key => $blockProperties)
    {
        $blockName = getBlockName($blockProperties["blockID"]);
        foreach ($blockProperties["inputs"] as $inputIndex => $type_value)
        {
            $type_block_output_splited = explode(":", $type_value);

            switch ($type_block_output_splited[0])
            {
                case "BLOCK":
                    $referencedTO_Name = getblockName($type_block_output_splited[1]);
                    $referencedTO_Output = $type_block_output_splited[2];

                    $connectionCode = str_replace("%CALLEDON%", $blockName, $setInputFromBlock_Template);
                    $connectionCode = str_replace("%INDEX_IN%", $inputIndex, $connectionCode);
                    $connectionCode = str_replace("%GETFROM%", $referencedTO_Name, $connectionCode);
                    $connectionCode = str_replace("%INDEX_OUT%", $referencedTO_Output, $connectionCode);

                    array_push($connectionList, $connectionCode);

                break;
                case "CONST":
                    $VALUE = $type_block_output_splited[1];

                    $connectionCode = str_replace("%CALLEDON%", $blockName, $setInputFromConstans_Template);
                    $connectionCode = str_replace("%INDEX_IN%", $inputIndex, $connectionCode);
                    $connectionCode = str_replace("%VALUE%", $VALUE, $connectionCode);

                    array_push($connectionList, $connectionCode);

                break;
            }
        }
    }
    return $connectionList;
}

function schedulers($blocksModel, $modulesModel, $blocksLibrary, $functionsTemplate){
    
    $runQueue = array();
     
    $runQueue += createRunForModules($modulesModel, $functionsTemplate); 
    $runQueue += createRunForBlocks($blocksModel, $blocksLibrary, $functionsTemplate);

    return $runQueue;
    
    
}

function createRunForModules($modulesModel, $runFunctionTemplate){
    $runModulesList = array();
    
    foreach ($modulesModel as $key => $moduleProperties) {
        
        $runCode = str_replace("%CALLEDON%", getModuleName($moduleProperties['moduleID']), $runFunctionTemplate);
        
        $runModulesList[getModuleName($moduleProperties['moduleID'])] = $runCode;
    }

    return $runModulesList;
}

function createRunForBlocks($blocksModel, $blocksLibrary, $runFunctionTemplate){
    
    $runBlocksList = array();

    $blocksQueue = startDFS($blocksModel, $blocksLibrary);
    
    foreach($blocksQueue as $key => $blockName){
        
        $runCode = str_replace("%CALLEDON%", $blockName, $runFunctionTemplate);
        
        $runBlocksList[$blockName] = $runCode;
    }

    return $runBlocksList;
    
}


//SCHEDULE HELPER STRUCTURE CLASS
//EACH ELEMENT HAS A LAST CALCULATED QUEUE NUMBER
//itemname: the c++ source code fragment for Construct the BLOCK
//queueNumber: calculated queue number (it can increase but not decrease)
class ScheduleStruct
{
  public $queueNumber = -1;
  public $itemName;

  function __construct($itemName, $queueNumber)
  {
    $this->itemName = $itemName;
    $this->queueNumber = $queueNumber;
  }

  function setQueueNumber($queueNumber)
  {
    if ($queueNumber > $this->queueNumber) {
      $this->queueNumber = $queueNumber;
      return true;
    }
    return false;
  }
}

function startDFS($blocksModel, $blocksLibrary){

    $scannedNodes = array();
    
    $firstLayer = getFirstLayerDFS($blocksModel, $blocksLibrary);
    $depth = 0;

    foreach ($firstLayer as $key => $blockProperties) {
        $dfsChain = array();
        
        runDFS($blocksModel, $blockProperties, $scannedNodes, $depth, $dfsChain);
    }

    //print_r(orderDFSOutput($scannedNodes));

    return (orderDFSOutput($scannedNodes));
}

function getFirstLayerDFS($blocksModel, $blocksLibrary){
    $isOutputFromSystem = array();

    foreach ($blocksModel as $key => $blockProperties) {
        if($blocksLibrary[$blockProperties['classID']]['output'] == true){
            array_push($isOutputFromSystem, $blockProperties);
        }
    }
    return $isOutputFromSystem;
}

function findBlockPropertyBy_blockID($blocksModel, $id){
    foreach ($blocksModel as $key => $blockProperties) {
        if($blockProperties['blockID'] == $id){
            return $blockProperties;
        }
    }
}

function runDFS($blocksModel, $blockProperties, &$scannedNodes, $depth, $dfsChain){

        if(in_array($blockProperties['blockID'], $dfsChain)){
            echo "Loop detected";
            return;
        }

        array_push($dfsChain, $blockProperties['blockID']);
    
        $blockName = getBlockName($blockProperties['blockID']);
        if(!array_key_exists($blockProperties['blockID'], $scannedNodes)){
            $scannedNodes[$blockProperties['blockID']] = new ScheduleStruct($blockName, $depth);
        }

        else if(!$scannedNodes[$blockProperties['blockID']]->setQueueNumber($depth)){
            //echo $scannedNodes[$blockProperties['blockID']]->setQueueNumber($depth);
            echo "Has higher value";
            return;
        }
        
            
        foreach ($blockProperties['inputs'] as $inputIndex => $possibleConnection) {
            
            //echo $blockProperties['blockID'] . " |-> " . $possibleConnection . "<br>";
            
            $type_block_output_splited = explode(":", $possibleConnection);
            if($type_block_output_splited[0] == "BLOCK"){
                $newBlockProperties = findBlockPropertyBy_blockID($blocksModel, $type_block_output_splited[1]);
                //echo $blockProperties['blockID'] . " |-> " . $possibleConnection . "<br>";
                runDFS($blocksModel, $newBlockProperties, $scannedNodes, $depth+1, $dfsChain);
            }
        }
}

function orderDFSOutput($nodes){

    $nodeGroups = array();

    foreach($nodes as $id => $node){
        $nodeGroups[$node->queueNumber][] = $node->itemName; 
    }

    //return $nodeGroups;

    return array_reverse(array_merge(...$nodeGroups));
    
}


?>