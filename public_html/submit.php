<?php

//Make sure we have a dump for the requested wiki
if(in_array($_POST['dump'],scandir('/public/datasets/public/')))
{

//encode the request in json
$json = json_encode($_POST);
//{"dump":"enwiki","nsinclude":["0"],"titlecontains":"","titlenotcontains":"\/","textcontains":"\\{\\{orphan(s|article)?\\}\\}","textnotcontains":"","textregex":"true"}

echo $json;

//work out the ids
$id = file_get_contents('next.id');
file_put_contents('next.id',strval($id+1));

//create our stuff
$dir = 'r/'.$id;
mkdir($dir);

file_put_contents($dir.'/status','{"status": "pending"}');
file_put_contents($dir.'/request',strval($json));
//RUN PYTHON IN GRID
echo "\nRunning $id , see http://tools.wmflabs.org/dumpscan/$dir";

}
else
{
	echo "Bad input";
}

?>
