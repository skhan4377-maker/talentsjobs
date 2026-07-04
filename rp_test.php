<?php
file_put_contents(
    __DIR__.'/rp_hit.log',
    "HIT ".date('Y-m-d H:i:s')."\n".file_get_contents('php://input')."\n\n",
    FILE_APPEND
);
http_response_code(200);
echo 'OK';
