<?php

if (empty($endpoint_id)) {
    response(403, 'You need to specify an ID to delete.');
} else {
    // Delete $endpoint_id from $endpoint
    echo $request . ' id:' . $endpoint_id . ' from ' . $endpoint;
}
