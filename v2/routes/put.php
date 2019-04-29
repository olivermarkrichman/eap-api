<?php

if (empty($endpoint_id)) {
    response(403, 'You need to specify an ID to edit.');
} else {
    // Edit $endpoint_id on $endpoint
    echo 'Edit id:' . $endpoint_id . ' on ' . $endpoint;
}
