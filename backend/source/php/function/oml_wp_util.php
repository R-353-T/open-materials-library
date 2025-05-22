<?php

function oml_wp_original_request(WP_REST_Request $request): bool
{
    return str_starts_with($request->get_route(), "/wp/");
}
