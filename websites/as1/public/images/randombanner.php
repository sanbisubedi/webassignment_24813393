<?php 
//Pick a file at random from the 'banners' directory and display it
$files = [];
// Loops through all files

// collects filenames
foreach (new DirectoryIterator('./banners') as $file) {
    // looking for .file
    if ($file->isDot()) {
        // continue if any dot file is found
        continue;
    }
    // if statemnent to check .jpg format images
    if (!strpos($file->getFileName(), '.jpg')) {
        // continue if jpg image is foung
        continue;
    }
    // Adds the filename of the JPEG image to the $files array for later use.

    $files[] = $file->getFileName();
}
// Set the Content-Type header to inform the browser that the output is a JPEG image
header('content-type: image/jpeg');

// Get the contents of a randomly selected image file from the './banners/' directory
$contents = file_get_contents('./banners/' . $files[rand(0,count($files)-1)]);

// Prevent the browser from caching the image (first Cache-Control header)
header("Cache-Control: no-store, no-cache, must-revalidate");

// Additional cache prevention directives (used mainly for compatibility with older browsers)
header("Cache-Control: post-check=0, pre-check=0", false);

// More caching prevention using the Pragma header (mainly for HTTP/1.0 compatibility)
header("Pragma: no-cache");

// Set the Content-Length header to the size of the image content (in bytes)
header('content-length: ' . strlen($contents));

// Output the image data to the browser
echo $contents;
