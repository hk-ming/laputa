<?php
use hyperqing\Password;

require_once __DIR__ . '/vendor/autoload.php';
echo Password::crypt('123456')."\n";
var_dump(Password::verify('123456', '$2y$10$9RTa6zmUkkYTVTHDkSNcU.4m8WJl/TA4eeSplFhc3ha904k/3o58u'));
