<?php

/**
 * Filters the expiration time of a JWT token
 *
 * @return int The expiration time in seconds
 */
function oml_jwt_expiration_time_filter()
{
    return OML_AUTH_EXPIRATION_TIME;
}

/**
 * Exposes additionnal CORS headers for API requests
 *
 * @param array $headers The headers
 * @param WP_REST_Request $request The request
 *
 * @return array The modified headers
 */
function oml_expose_cors_headers_filter(array $headers, WP_REST_Request $request)
{
    if (oml_wp_original_request($request) === false) {
        $headers[] = "X-RateLimit-Limit";
        $headers[] = "X-RateLimit-Remaining";
        $headers[] = "Retry-After";
    }

    return $headers;
}

/**
 * Blocks all original WordPress endpoints if the user is not logged in
 *
 * @param array $endpointList The list of registered endpoints
 *
 * @return array List of allowed endpoints
 */
function oml_wp_block_original_endpoints_filter(array $endpointList)
{
    $namespaceList = [OML_AUTH_ENDPOINT, OML_NAMESPACE];

    if (is_user_logged_in() === false) {
        foreach (array_keys($endpointList) as $endpoint) {
            $match = false;

            foreach ($namespaceList as $namespace) {
                if (fnmatch("/" . $namespace . "/*", $endpoint, FNM_CASEFOLD)) {
                    $match = true;
                }
            }

            if (!$match) {
                unset($endpointList[$endpoint]);
            }
        }
    }

    return $endpointList;
}
