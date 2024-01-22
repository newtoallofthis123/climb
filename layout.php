<?php

namespace ClimbUI;

require_once __DIR__ . '/support/lib/vendor/autoload.php';

use \Approach\Render;
use \ClimbUI\Render as ProjectRender;

$webpage = new Render\HTML(tag: 'html');
$webpage->before = '<!DOCTYPE html>' . PHP_EOL;

$head = new Render\HTML(tag: 'head');
$body = new Render\HTML(tag: 'body');

$myList = new Render\HTML(tag: 'ul', classes: ['my-list']);
$complexItem = new Render\HTML(
    tag: 'li',
    classes: ['complex-item'],
    content: 'Complex item'
);

for ($i = 0, $L = 10; $i < $L; $i++) {
    $item = clone $complexItem;
    $item->content = 'Item ' . $i;
    $myList[] = $item;
}

$data = [
    'name' => 'John Doe',
    'age' => 30,
    'email' => 'johndoe@example.com'
];

$encoded_data = json_encode($data);

/*
$textList = new Render\Node();
for ($i = 0, $L = 10; $i < $L; $i++) {
	// $textList-> content = 'Item ' . $i;
	$item = new Render\Node( content: 'Item ' . $i );
	$textList[] = $item;
}

/**
	
$textList
	->nodes = [
		item 0
		item 1..
	]

*/

$aExample = new Render\HTML(
    tag: 'a',
    classes: ['yt'],
    attributes: ['href' => 'https://youtube.com'],
    content: 'YouTUBE!!'
);

$webpage[] = $head;
$body[] = $myList;
$body[] = $aExample;
$body[] = new ProjectRender\UserProfile(
    img_src: 'https://noobscience.vercel.app/favicon.ico',
    heading: 'John Doe',
    username: 'Administrator'
);
$webpage[] = $body;


// :before <tag attr classes>prefix content [child nodes] suffix</tag> :after
