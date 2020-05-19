<?php

//Declaring stuff, ignore this.
$webring = array();
$webring['following'] = array();
$webring['known'] = array();
$webring['blacklist'] = array();
$webring['boards'] = array();
$webring['logo'] = array();

/*------------------------USER CONFIG STARTS HERE------------------------*/

//Site display name
$webring['name'] = '4Feuilles.org';

//Site base URL
$webring['url'] = 'https://4feuilles.org';

//Location of the webring json
//This is so that this node in the webring has a way to know its location and thus not follow itself
//Defaults to /webring.json
$webring['endpoint'] = 'https//planches.4feuilles.org/webring.json';

//Site logo(s)
//Can have more than one
//Defaults to default vichan location
$webring['logo'][] = $webring['url'] . '/favicon.ico';
//$webring['logo'][] = 'https://image.board/anotherlogo.png';

//Other sites in the ring to be followed (add as few/many as desired)
$webring['following'][] = 'https://fch.bet/webring.json';

//Domains to be blacklisted and not followed or parsed (add as few/many as desired)
//Note that this should just be the domain itself, no https:// or anything
$webring['blacklist'][] = 'planches.4feuilles.org';
$webring['blacklist'][] = 'frchan.bet';


