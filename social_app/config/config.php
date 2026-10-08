<?php
/**
 * Application settings.
 */
return [
    'app_name'         => 'Mini Social',
    'timezone'         => 'Asia/Manila',   // PHP timezone
    'db_timezone'      => '+08:00',        // must match the PHP timezone above (used for MySQL NOW())
    'posts_per_page'   => 10,
    'max_upload_bytes' => 2 * 1024 * 1024, // 2 MB per image
    'debug'            => true,            // set to false in production (hides error details)
];
