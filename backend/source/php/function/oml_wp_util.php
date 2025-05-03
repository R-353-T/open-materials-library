<?php

/**
 * Checks if a request is an original request to WordPress
 *
 * @param WP_REST_Request $request The request to check
 *
 * @return bool
 */
function oml_wp_original_request(WP_REST_Request $request)
{
    return str_starts_with($request->get_route(), "/wp/");
}
