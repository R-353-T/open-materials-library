<?php

namespace oml\api\middleware;

use oml\php\abstract\Middleware;
use oml\php\core\LoginLimitExceededResponse;
use WP_HTTP_Response;
use WP_REST_Server;
use WP_REST_Request;

class AuthLimitMiddleware extends Middleware
{
    /**
     * Checks if the user has been jailed due to too many login attempts
     *
     * @param mixed $response The wordpress response
     * @param WP_REST_Server $server The server rest api server
     * @param WP_REST_Request $request The wordpress request
     *
     * @return mixed The response
     */
    public function request(mixed $response, WP_REST_Server $server, WP_REST_Request $request)
    {
        if (
            $response === null
            && $request->get_route() === OML_AUTH_LOGIN_ENDPOINT
            && $request->get_method() !== "OPTIONS"
        ) {
            $userInfo = $this->updateUserInfo();

            if ($userInfo["jailed"]) {
                $response = new LoginLimitExceededResponse();
            }
        }

        return $response;
    }

    /**
     * This middleware is used to limit the number of login attempts to the API
     *
     * @param WP_HTTP_Response $response The wordpress response
     * @param WP_REST_Server $server The server rest api server
     * @param WP_REST_Request $request The wordpress request
     *
     * @return WP_HTTP_Response The response
     */
    public function response(WP_HTTP_Response $response, WP_REST_Server $server, WP_REST_Request $request)
    {
        if (
            $request->get_route() === OML_AUTH_LOGIN_ENDPOINT
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
                    $response->header("Retry-After", OML_API_JAIL_TIME);
                }

                $response->header("X-RateLimit-Limit", OML_API_LOGIN_ATTEMPT_LIMIT);
                $response->header("X-RateLimit-Remaining", OML_API_LOGIN_ATTEMPT_LIMIT - $userInfo["attemps"]);
                $data["error"] = "auth_forbidden";
                unset($data["message"]);
                unset($data["status"]);
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
            set_transient($this->getTransientName(), $userInfo, OML_API_JAIL_TIME);
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
        $userInfo["jailed"] = $userInfo["attemps"] > OML_API_LOGIN_ATTEMPT_LIMIT;

        if ($userInfo["jailed"]) {
            $userInfo["attemps"] = OML_API_LOGIN_ATTEMPT_LIMIT;
        }

        set_transient($this->getTransientName(), $userInfo, OML_API_JAIL_TIME);
        return $userInfo;
    }
}
