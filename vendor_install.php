<?php
if (file_exists(__DIR__."/vendor")):
    exit;
elseif (file_exists(__DIR__."/../../vendor")):
    exit;
endif;

copy(
    "https://raw.githubusercontent.com/Mediashare/time-tracking/master/time-tracking.phar", 
    __DIR__."/../../time-tracking.phar"
);
echo "Time Tracking was installed. \n";