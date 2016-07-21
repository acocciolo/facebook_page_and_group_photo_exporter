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
      <div class="one-half column" >
   
   
        <h4>Download Photos on Windows - Step by Step Guide</h4>
       
       <ol>
       <li><a href="csv/<?php echo $file;?>">Download the Batch Script file</a>.  This file has commands for 
       downloading the images from Facebook. 
       
      <li>The batch script requires the use of WGet.  
      <a href="http://downloads.sourceforge.net/gnuwin32/wget-1.11.4-1-setup.exe">Download WGet</a> 
      and follow the installation instrucitons.  Pay attention to where you installed it to (e.g., c:\GnuWin32\).
      
      <li>You have to register WGet's path with the system variables so the script knows where to find Wget.  To do this, right click "My Computer"
      and Select System Settings or Properties.  Select an option for "Avanced System Settings."  Select the "Environment Variables" option.  In the System Variable "Path", append the WGet path, such as:
      
      <code>c:\windows\system32\;c:\gnuwin32\bin</code> 
      
        <li>Double click the .bat file.  If you receive error messages, that means that it probably can't find WGet, and something did not go correct when adding it to the path variables.  If you open the command-prompt (Start -> Run -> cmd), and type in wget and you get an error, then WGet is not installed or not registered in the system paths.  Try again the earlier step.  

       
      <li>That's it!  When the batch file is complete, you will have a copy of your photos with the metadata CSV file.

    
       
       </ol>
       
       
          </p>
</div>
</div>
</div>

<?php require_once("footer.php"); ?>