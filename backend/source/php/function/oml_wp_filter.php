<?php

function oml_jwt_expiration_time_filter(): int
{
    return ___AUTH_EXPIRATION_TIME___;
}

function oml_expose_cors_headers_filter(array $header_list, WP_REST_Request $request): array
{
    if (oml_wp_original_request($request) === false) {
        $header_list[] = "X-RateLimit-Limit";
        $header_list[] = "X-RateLimit-Remaining";
        $header_list[] = "Retry-After";
    }

    return $header_list;
}

function oml_wp_block_original_endpoints_filter(array $endpoint_list): array
{
    $namespaceList = [
        ___AUTH_ENDPOINT___,
        ___NAMESPACE___
    ];

    if (is_user_logged_in() === false) {
        foreach (array_keys($endpoint_list) as $endpoint) {
            $match = false;

            foreach ($namespaceList as $namespace) {
                if (fnmatch("/" . $namespace . "/*", $endpoint, FNM_CASEFOLD)) {
                    $match = true;
                }
            }

            if (!$match) {
                unset($endpoint_list[$endpoint]);
            }
        }
    }

    return $endpoint_list;
}
