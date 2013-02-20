AssetBundler
============

AssetBundler is a PHP class for managing assets (javascript files, css files, images, etc.).

Most modern web applications use javascript frameworks (e.g. jQuery, Mootools, etc.).  Sites will also typically have anywhere from 2 to 20+ plugins or other script files in use throughout various pages.

If these assets aren't managed properly, the performance of your site can suffer.

AssetBundler aims to solve 2 problems:

1.    How should I best group together assets to increase performance?
2.    Once I have these pre-compiled "bundles", how do I figure out which ones to use for each page request?

How it Works
==============

To get started, include `AssetBundler.php` and create an `AssetBundler` object.
```php
require "AssetBundler.php";
$bundler = new AssetBundler();
```

Generating Bundles
-------------------

The first step is to figure out how to best group assets together into bundles.
This is an optimization problem with several competing parameters:
*    The number of http requests made on a single page should be kept to a minimum
*    The file size of the bundles should be kept to a minimum
*    The same bundles should be re-used across pages when possible to take advantage of caching

AssetBundler provides a __learn__ method that takes as input groups of assets commonly requested together.  
With this training data, AssetBundler is able to construct bundles with the optimization problem in mind.

```php
// The first argument is a list of files
// The second argument is how many times that list of files was requested together
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

// Generate the bundles
$bundler->generateBundles();
```

If you already know how you want to group assets together, you can bypass the learning step and set the bundles directly.

```php
$bundler->setBundles(array(
  'bundle1_name.js'=>array(
    'file1.js',
    'file2.js',
  ),
  'bundle2_name.js'=>array(
    'file3.js',
    'file4.js',
  ),
));
```

Using Bundles
-------------------

The second step is to determine which bundles to use for the current request.
AssetBundler provides a __bundle__ method that swaps out individual files for bundled versions when appropriate.

```php
// The files for the current request
$files = array(
  'jquery.js',
  'base.js',
  'lightbox.js',
  'weather_widget.js',
  'unknown_file.js',
);

// A new list of files using bundles when possible
$new_files = $bundler->bundle($files);
```
