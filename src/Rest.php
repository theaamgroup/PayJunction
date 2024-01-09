<?php

namespace AAM\PayJunction;

use Exception;

class Rest
{
    private $api_domain = '';
    private $sdk_url = '';
    private $app_key = '';
    private $api_login = '';
    private $api_password = '';
    private $curl_errno = 0;
    private $curl_error = '';
    private $curl_status_code = 0;
    private $success = false;
    private $error_messages = [];
    private $result = [];
    private $debug_message = '';

    public function __construct(string $app_key, string $api_login, string $api_password, bool $use_sandbox = false)
    {
        $this->app_key = $app_key;
        $this->api_login = $api_login;
        $this->api_password = $api_password;
        $this->useSandbox($use_sandbox);
    }

    public function useSandbox(bool $use_sandbox): void
    {
        $domain = $use_sandbox ? 'payjunctionlabs' : 'payjunction';
        $this->api_domain = "https://api.$domain.com/";
        $this->sdk_url = "https://www.$domain.com/trinity/js/sdk.js";
    }

    public function setAppKey(string $app_key): void
    {
        $this->app_key = $app_key;
    }

    public function setCredentials(string $api_login, string $api_password): void
    {
        $this->api_login = $api_login;
        $this->api_password = $api_password;
    }

    public function get(string $endpoint, array $data = [])
    {
        return $this->call('GET', $endpoint, $data);
    }

    public function post(string $endpoint, array $data = [])
    {
        return $this->call('POST', $endpoint, $data);
    }

    public function put(string $endpoint, array $data = [])
    {
        return $this->call('PUT', $endpoint, $data);
    }

    public function delete(string $endpoint)
    {
        return $this->call('DELETE', $endpoint);
    }

    private function call(string $method, string $endpoint, array $data = [])
    {
        $this->result = null;
        $this->success = false;
        $this->error_messages = [];
        $this->debug_message = '';

        if (!$this->api_domain) {
            throw new Exception('Payment API domain must be set');
        }

        if (!$this->app_key) {
            throw new Exception('Payment API application key must be set');
        }

        if (!$this->api_login || !$this->api_password) {
            throw new Exception('Payment API credentials must be set');
        }

        $method = strtoupper($method);
        $request_url = $this->api_domain . $endpoint;
        $data_string = '';

        if ($data) {
            $data_params = [];

            foreach ($data as $key => $val) {
                if (is_array($val)) {
                    $val_items = $val;

                    foreach ($val_items as $val) {
                        $data_params[] = $key . '[]=' . urlencode(json_encode($val));
                    }
                } else {
                    $data_params[] = $key . '=' . urlencode($val);
                }
            }

            $data_string = implode('&', $data_params);
        }

        if ($method === 'GET' && $data_string) {
            $request_url .= '?' . $data_string;
        }

        $ch = curl_init($request_url);
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        $authorization = base64_encode("{$this->api_login}:{$this->api_password}");

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/x-www-form-urlencoded",
            'Accept: application/json',
            "Authorization: Basic $authorization",
            "X-PJ-Application-Key: {$this->app_key}",
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
        }

        if ($method !== 'GET' && $data_string) {
            curl_setopt(
                $ch,
                CURLOPT_POSTFIELDS,
                $data_string
            );
        }

        $response = curl_exec($ch);
        $this->curl_errno = curl_errno($ch);
        $this->curl_error = curl_error($ch);
        $this->curl_status_code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $this->result = json_decode($response, true);
        curl_close($ch);
        $errors = $this->result['errors'] ?? [];
        $bad_status = !in_array($this->curl_status_code, [200, 204]);

        if ($bad_status) {
            $this->success = false;

            $this->debug_message = "[$method/$endpoint - " . $this->curl_status_code . "] "
                . $this->getStatusCodeMessage() . "\n"
                . "Request URL: " . $request_url . "\n"
                . "Request Data: " . ($data_string ?: 'empty') . "\n"
                . "Response Data: " . ($this->result ? print_r($this->result, true) : 'empty');

            if (!empty($errors)) {
                foreach ($errors as $error) {
                    $parameter = $error['parameter'] ?? '';
                    $message = $error['message'] ?? '';

                    if ($parameter && $message) {
                        $message = $parameter . ': ' . $message;
                    }

                    $this->error_messages[] = $message;
                }

                return $this->error_messages;
            }

            if ($this->result === null) {
                throw new Exception("[$method/$endpoint - " . $this->curl_status_code . "]"
                    . " Received empty response from payment API");
            }
        } else {
            $this->success = true;
        }

        return $this->result;
    }

    private function getStatusCodeMessage(): string
    {
        $status_codes = [
            200 => 'Success',
            204 => 'No Content',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            500 => 'Internal Server Error',
        ];

        return $status_codes[$this->curl_status_code] ?? 'Unknown Status Code';
    }

    public function getDebugMessage(): string
    {
        return $this->debug_message;
    }

    public function getCurlErrno(): int
    {
        return $this->curl_errno;
    }

    public function getCurlError(): string
    {
        return $this->curl_error;
    }

    public function getCurlStatusCode(): int
    {
        return $this->curl_status_code;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getResult(string $value = '')
    {
        if (!$value) {
            return is_array($this->result) ? $this->result : [];
        }

        return $this->result[$value] ?? null;
    }

    public function getErrorMessages(): array
    {
        return $this->error_messages;
    }

    public function getSdkUrl(): string
    {
        return $this->sdk_url;
    }
}
