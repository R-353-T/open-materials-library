<?php

namespace oml\api\middleware;

use oml\php\abstract\Middleware;
use oml\php\error\TooManyRequestsError;
use WP_HTTP_Response;
use WP_REST_Server;
use WP_REST_Request;

class BucketMiddleware extends Middleware
{
    public function request(
        mixed $response,
        WP_REST_Server $server,
        WP_REST_Request $request
    ): mixed {
        if ($response === null && $request->get_method() !== "OPTIONS") {
            $bucket = $this->updateBucket();

            if ($bucket["count"] === 0) {
                return new TooManyRequestsError();
            }
        }

        return $response;
    }

    public function response(
        WP_HTTP_Response $response,
        WP_REST_Server $server,
        WP_REST_Request $request
    ): WP_HTTP_Response {
        if ($request->get_method() !== "OPTIONS") {
            $bucket = $this->getBucket();

            if ($response->get_status() === 429 && $bucket["count"] === 0) {
                $response->header("Retry-After", ___JAIL_TIME___);
            }

            $response->header("X-RateLimit-Limit", ___CALL_LIMIT___);
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
            $bucket = ["count" => ___CALL_LIMIT___, "updatedAt" => time()];
            set_transient($this->getTransientName(), $bucket, ___JAIL_TIME___);
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

        if ($elapsed < ___JAIL_TIME___ && $bucket["count"] === 0) {
            $bucket["updatedAt"] = $now;
        } else {
            $plus = floor(___CALL_LIMIT___ / ___CALL_INTERVAL___) * $elapsed;
            $bucket["count"]--;

            if ($plus > 0) {
                $bucket["count"] += $plus;
                $bucket["updatedAt"] = $now;

                if ($bucket["count"] > ___CALL_LIMIT___) {
                    $bucket["count"] = ___CALL_LIMIT___;
                }
            }

            if ($bucket["count"] < 0) {
                $bucket["count"] = 0;
            }
        }

        set_transient($this->getTransientName(), $bucket, ___JAIL_TIME___);
        return $bucket;
    }
}
