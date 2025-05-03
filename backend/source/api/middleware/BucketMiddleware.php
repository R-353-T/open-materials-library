<?php

namespace oml\api\middleware;

use oml\php\abstract\Middleware;
use oml\php\core\CallLimitExceededResponse;
use WP_HTTP_Response;
use WP_REST_Server;
use WP_REST_Request;

class BucketMiddleware extends Middleware
{
    /**
     * This middleware is used to limit the number of calls to the API
     *
     * @param mixed $response The wordpress response
     * @param WP_REST_Server $server The server rest api server
     * @param WP_REST_Request $request The wordpress request
     *
     * @return mixed The response
     */
    public function request(mixed $response, WP_REST_Server $server, WP_REST_Request $request)
    {
        if ($response === null && $request->get_method() !== "OPTIONS") {
            $bucket = $this->updateBucket();

            if ($bucket["count"] === 0) {
                return new CallLimitExceededResponse();
            }
        }

        return $response;
    }

    /**
     * This middleware is used to limit the number of calls to the API
     *
     * @param WP_HTTP_Response $response The wordpress response
     * @param WP_REST_Server $server The server rest api server
     * @param WP_REST_Request $request The wordpress request
     *
     * @return WP_HTTP_Response The response
     */
    public function response(WP_HTTP_Response $response, WP_REST_Server $server, WP_REST_Request $request)
    {
        if ($request->get_method() !== "OPTIONS") {
            $bucket = $this->getBucket();

            if ($response->get_status() === 429 && $bucket["count"] === 0) {
                $response->header("Retry-After", OML_API_JAIL_TIME);
            }

            $response->header("X-RateLimit-Limit", OML_API_CALL_LIMIT);
            $response->header("X-RateLimit-Remaining", $bucket["count"]);
        }

        return $response;
    }

    /**
     * Gets the name of the transient that stores the bucket
     *
     * @return string The name of the transient
     */
    private function getTransientName()
    {
        return $this->userUid . DIRECTORY_SEPARATOR . "bucket";
    }

    /**
     * Gets the user's bucket, creating it if it doesn't exist
     *
     * @return array The user's bucket
     */
    private function getBucket()
    {
        $bucket = get_transient($this->getTransientName());

        if ($bucket === false) {
            $bucket = ["count" => OML_API_CALL_LIMIT, "updatedAt" => time()];
            set_transient($this->getTransientName(), $bucket, OML_API_JAIL_TIME);
        }

        return $bucket;
    }

    /**
     * Updates the user's bucket
     *
     * @return array The updated user's bucket
     */
    private function updateBucket()
    {
        $bucket = $this->getBucket();
        $now = time();
        $elapsed = $now - $bucket["updatedAt"];

        if ($elapsed < OML_API_JAIL_TIME && $bucket["count"] === 0) {
            $bucket["updatedAt"] = $now;
        } else {
            $plus = floor(OML_API_CALL_LIMIT / OML_API_CALL_INTERVAL) * $elapsed;
            $bucket["count"]--;

            if ($plus > 0) {
                $bucket["count"] += $plus;
                $bucket["updatedAt"] = $now;

                if ($bucket["count"] > OML_API_CALL_LIMIT) {
                    $bucket["count"] = OML_API_CALL_LIMIT;
                }
            }

            if ($bucket["count"] < 0) {
                $bucket["count"] = 0;
            }
        }

        set_transient($this->getTransientName(), $bucket, OML_API_JAIL_TIME);
        return $bucket;
    }
}
