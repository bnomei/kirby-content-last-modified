<?php

if (php_sapi_name() !== 'cli') {
    echo site()->contentLastModified()->toDate('c');
}
