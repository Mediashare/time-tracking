<?php
if (file_exists(__DIR__."/vendor")):
    exit;
elseif (file_exists(__DIR__."/../../vendor")):
    exit;
endif;

copy($time_tracking, __DIR__."/../../time-tracking.phar");
echo "Time Tracking was installed. \n";