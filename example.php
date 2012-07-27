<?php
require "AssetBundler.php";
$bundler = new AssetBundler();

//prep the Bundler with some data
//the first argument is a list of files
//the second argument is how many times that list of files was requested
$bundler->learn(array(
	'jquery.js',
	'base.js',
	'lightbox.js',
	'weather_widget.js',
),100);
$bundler->learn(array(
	'jquery.js',
	'base.js',
	'lightbox.js',
),23);
$bundler->learn(array(
	'jquery.js',
	'base.js',
	'forms.js',
	'weather_widget.js',
	'contact_us_validation.js',
),8);
$bundler->learn(array(
	'jquery.js',
	'base.js',
	'forms.js',
	'request_quote_validation.js',
	'lightbox.js',
),14);

//generate bundles from this data
//this will try to choose bundles intelligently based on what files are normally requested together
$bundler->generateBundles();
echo "Bundles:<pre>".print_r($bundler->getBundles(),true)."</pre>";


//if you already know the bundles you want to use you can set them directly
/*
$bundler->setBundles(array(
	'bundle1_name'=>array(
		'file1.js',
		'file2.js',
	),
	'bundle2_name'=>array(
		'file3.js',
		'file4.js',
	),
));
*/


//these are the files being requested for a specific page view
$files = array(
	'jquery.js',
	'base.js',
	'lightbox.js',
	'weather_widget.js',
	'unknown_file.js',
);
echo "Original File List:<pre>".print_r($files,true)."</pre>";

//make this list use bundles where it makes sense
$new_files = $bundler->bundle($files);
echo "New File List:<pre>".print_r($new_files,true)."</pre>";
