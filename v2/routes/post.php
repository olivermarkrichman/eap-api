<?php

if (empty($endpoint_id)) {
    // Create new item on $endpoint
    echo $request . ' new ' . $endpoint;
} else {
    response(403, 'Posting to a specific ID is not allowed.');
}
