<?php
// Copy this file to the public_html directory and point it to the script folder
// It's safer than putting the all the script in a public accessible folder
chdir("/home/mydomain/.sslscriptfolder");
require("index.php");