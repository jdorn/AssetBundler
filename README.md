AssetBundler
============

Asset Builder is a PHP class for managing assets (javascript files, css files, images, etc.).

Most modern web applications use javascript frameworks (e.g. jQuery, Mootools, etc.).  Sites will also typically have anywhere from 2 to 20+ plugins or other script files in use throughout various pages.

If these assets aren't managed properly, the performance of your site can suffer.

AssetBuilder aims to solve 2 problems:
#    How should I group together assets to increase performance?
#    Once I have these pre-compiled "bundles", how do I figure out which ones to use for each page?

How it Works
==============

First, AssetBundler breaks up all your assets into bundles.  
It comes with a learning system that will automatically do this based on training data you supply.  
If you already know the bundles you want to use, you can bypass the learning system and set them directly.

Then, when a page is requested, you pass in all the asset files you need to AssetBundler and it will replace individual files with the bundled versions when appropriate.
