<?php

header("HTTP/1.0 404 Not Found");
echo "Error 404: File not found - " . htmlspecialchars($this->currentPath);
