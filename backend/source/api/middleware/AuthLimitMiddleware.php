<?php

namespace oml\api\middleware;

use oml\api\enum\APIError;
use oml\php\abstract\Middleware;
use oml\php\error\TooManyRequestsError;
use WP_HTTP_Response;
use WP_REST_Server;
use WP_REST_Request;

class AuthLimitMiddleware extends Middleware
{
    public function request(
        mixed $response,
        WP_REST_Server $server,
        WP_REST_Request $request
    ): mixed {
        if (
            $response === null
            && $request->get_route() === ___AUTH_TOKEN_ENDPOINT___
            && $request->get_method() !== "OPTIONS"
        ) {
            $userInfo = $this->updateUserInfo();

            if ($userInfo["jailed"]) {
                $response = new TooManyRequestsError();
            }
        }

        return $response;
    }

    public function response(
        WP_HTTP_Response $response,
        WP_REST_Server $server,
        WP_REST_Request $request
    ): WP_HTTP_Response {
        if (
            $request->get_route() === ___AUTH_TOKEN_ENDPOINT___
            && $request->get_method() !== "OPTIONS"
        ) {
            $data = $response->get_data();
            $userInfo = $this->getUserInfo();
            $status = $response->get_status();

            if ($status === 200) {
                $user = get_user_by("email", $data["user_email"]);
                $data["user_role"] = $user->roles[0];
                $this->deleteUserInfo();
            } elseif ($status === 429 || $status === 403) {
                if ($data["data"]["jailed"] === true) {
                    $response->header("Retry-After", ___JAIL_TIME___);
                }

                $response->header("X-RateLimit-Limit", ___AUTH_ATTEMPT_LIMIT___);
                $response->header("X-RateLimit-Remaining", ___AUTH_ATTEMPT_LIMIT___ - $userInfo["attemps"]);
                $data[___API_ERROR_KEY___] = APIError::FORBIDDEN;
                $data["message"] = APIError::FORBIDDEN_MESSAGE;
            }

            $response->set_data($data);
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
        return $this->userUid . DIRECTORY_SEPARATOR . "auth-limit";
    }

    /**
     * Gets the user's login attempt information from the transient.
     *
     * @return array The user's login attempt information
     */
    private function getUserInfo(): array
    {
        $userInfo = get_transient($this->getTransientName());

        if ($userInfo === false) {
            $userInfo = ["attemps" => 0, "jailed" => false];
            set_transient($this->getTransientName(), $userInfo, ___JAIL_TIME___);
        }

        return $userInfo;
    }

    /**
     * Deletes the user's login attempt information
     */
    private function deleteUserInfo(): void
    {
        delete_transient($this->getTransientName());
    }

    /**
     * Updates the user's login attempt information
     *
     * @return array The updated user's login attempt information
     */
    private function updateUserInfo(): array
    {
        $userInfo = $this->getUserInfo();
        $userInfo["attemps"]++;
        $userInfo["jailed"] = $userInfo["attemps"] > ___AUTH_ATTEMPT_LIMIT___;

        if ($userInfo["jailed"]) {
            $userInfo["attemps"] = ___AUTH_ATTEMPT_LIMIT___;
        }

        set_transient($this->getTransientName(), $userInfo, ___JAIL_TIME___);
        return $userInfo;
    }
}
