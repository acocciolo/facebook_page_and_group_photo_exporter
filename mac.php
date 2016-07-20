<?php
session_start();

require_once("header.php");

$out = "";
$csv = "";
$bat = "";
$sh = "";
$file = $_GET["file"];

?>

  <!-- Primary Page Layout
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <div class="container">
    <div class="row">
      <div class="one-half column">
   
   
        <h4>Download Photos on a Mac - Step by Step Guide</h4>
       
       <ol>
       <li><a href="csv/<?php echo $file;?>">Download the Shell Script file</a>.  This file has commands for 
       downloading the images from Facebook. 
       
       <li>In the Finder, rename the .sh file to something easier, like "download.sh"
       
       <li>Open the Mac's Terminal application. You can find it under Application -> Utilities -> Terminal.
       
    
    	<li> Change the current working directory to your downloads folder, by issuing the command:<br>
        <code>cd Downloads</code>
        
        
        
        <li>Make the script executable by typing in:<br>
        <code>chmod u+x download.sh</code>
       
        <li>Run the script by typing in:<br>
        <code>./download.sh</code>
        <li>That's it!  When it is done download, you will have a copy of your photos with the metadata CSV file.

       </ol>
       
       <a href="javascript:history.go(-1);">Back</a>
       
          </p>
</div>
</div>
</div>

<?php require_once("footer.php"); ?>