<?php

require_once('/webring-config.php');  // you need to put the full path

require_once('/mysql-core.php');  // you need to put the full path

$webRingPath = '/webring.json';  // you need to put the full path
$knownHostsPath = '/known_hosts.json';  // you need to put the full path
$outputPath = '/compiled_webring.json';  // you need to put the full path


function getLocalBoards($url){

    $url = 'url_imageboard';
    
    $short = array('/url1/','/url2/');
    
    $short1 = array('shortname1','shortname2');
    
    $long = array('name1','name1');
    
    $nametable = array('table1','table2');
    
    
    

    $boards = array();
    for ($i = 0; $i < 7; $i++){
        
        if (! $result = mysqli_call("select max(no) from " . $nametable[$i])) {
            echo S_SQLFAIL;
        }
        $row = mysqli_fetch_array($result);
        $lastno = (int) $row[0];
        mysqli_free_result($result);
        
        
        $item['postsPerHour'] = '';
        $item['uniqueUsers'] ='';
        $item['totalPosts'] = $lastno;
        
        if (! $result = mysqli_call("select max(time) from " . $nametable[$i])) {
            echo S_SQLFAIL;
        }
        $row = mysqli_fetch_array($result);
        $lastpost = (int) $row[0];
        mysqli_free_result($result);
        
        

       
        $item['uri'] = $short1[$i];
        $item['title'] = $long[$i];
        $item['subtitle'] = '';
        $item['path'] = $url . $short[$i];
        $item['nsfw'] = true;
        $datetime = new DateTime("@$lastpost");
        $item['lastPostTimestamp'] = $datetime->format(DateTime::ATOM);
        
        $boards[] = $item;
        
    }
    return $boards;
}

function isKnownHost($currentHost, $knownHosts){
    for ($j = 0; $j < count($knownHosts); $j++){
        if (!empty(parse_url($currentHost)['host']) && parse_url($currentHost)['host'] == parse_url($knownHosts[$j])['host']){
            return TRUE;
        }
    }
    return FALSE;
}

function isBlacklisted($currentHost, $knownHosts){
    for ($j = 0; $j < count($knownHosts); $j++){
        if (!empty(parse_url($currentHost)['host']) && parse_url($currentHost)['host'] == $knownHosts[$j]){
            return TRUE;
        }
    }
    return FALSE;
}

$knownHostsFile = @file_get_contents($knownHostsPath);

/* Only needed for working out if the blacklist has been updated TODO
$baseJsonFile = file_get_contents($webRingPath);
$baseJson = json_decode($baseJsonFile, TRUE);
if ($baseJson == NULL || $baseJson == FALSE){
    console.log('Missing or malformed webring.json.');
    die();
}*/

//The "known" field is now deprecated for security issues, for legacy reasons it will still be included in the produced webring.json however it will no longer be considered when spidering the webring.
//$knownHosts = @json_decode($knownHostsFile, TRUE);
$knownHosts = NULL;
if ($knownHosts == NULL || $knownHosts == FALSE)
    $knownHosts = array();

for ($i = 0; $i < count($webring['following']); $i++)
    if (!isKnownHost($webring['following'][$i], $knownHosts))
        $knownHosts[] = $webring['following'][$i];

$compiledJson = array();

for ($i = 0; $i < count($knownHosts); $i++){
    $currentRingFile = @file_get_contents($knownHosts[$i]);
    if ($currentRingFile == FALSE)
        continue;
    $currentRingJson = @json_decode($currentRingFile, TRUE);
    if ($currentRingJson == FALSE)
        continue;
    
    $compiledJson[] = $currentRingJson;

    for ($j = 0; !empty($currentRingJson['following']) && $j < count($currentRingJson['following']); $j++){
        if (!isKnownHost($currentRingJson['following'][$j], $knownHosts) && !isBlacklisted($currentRingJson['following'][$j], $webring['blacklist'])){
            if ($currentRingJson['following'][$j] != $webring['endpoint']){
                $knownHosts[] = $currentRingJson['following'][$j];
            }
        }
    }
}
$webring['known'] = $knownHosts;
$webring['boards'] = getLocalBoards($webring['url']);

//$compiledJson[] = $webring; TODO insert the webring base at the start

file_put_contents($webRingPath, json_encode($webring));
file_put_contents($knownHostsPath, json_encode($knownHosts));
file_put_contents($outputPath, json_encode($compiledJson));

die();

