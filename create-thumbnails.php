<?php

$allow = 1;
if (!$allow) die("This script is disabled");

require_once "class.thumbnail.php";

$items = array_diff(scandir("gallery"), [".", "..", ".DS_Store"]);
mkdir("thumbnails");
foreach ($items as $image) {
    $thumbnail = new Thumbnail();
    $thumbnail->setFile("gallery/$image");
    $thumbnail->scaleTo([
		"width" => 180,
		"keepProportion" => true,
	]);
	$thumbnail->output("thumbnails/$image");
    echo "<div>$image</div>";
}

echo "Done";