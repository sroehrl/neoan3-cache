<?php
require '../Cache.php';

\Neoan3\Apps\Cache::setCaching('-30 second', ['.php']);
echo "original content";
\Neoan3\Apps\Cache::write();