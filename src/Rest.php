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
    private $content_type = 'application/x-www-form-urlencoded';

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

    public function useJsonContentType(): void
    {
        $this->content_type = 'application/json';
    }

    public function useFormContentType(): void
    {
        $this->content_type = 'application/x-www-form-urlencoded';
    }

    public function get(string $endpoint)
    {
        return $this->call('GET', $endpoint);
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

        if ($method === 'GET' && $data) {
            $request_url .= '?' . http_build_query($data);
        }

        $ch = curl_init($request_url);
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        $authorization = base64_encode("{$this->api_login}:{$this->api_password}");

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: {$this->content_type}",
            'Accept: application/json',
            "Authorization: Basic $authorization",
            "X-PJ-Application-Key: {$this->app_key}",
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
        }

        if ($method !== 'GET' && $data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $this->curl_errno = curl_errno($ch);
        $this->curl_error = curl_error($ch);
        $this->curl_status_code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $this->result = json_decode($response, true);
        curl_close($ch);
        $errors = $this->result['errors'] ?? [];

        if (!in_array($this->curl_status_code, [200, 204]) && !empty($errors)) {
            foreach ($errors as $error) {
                $this->error_messages[] = $error['message'];
            }

            return $this->error_messages;
        }

        if ($this->result === null) {
            throw new Exception('Received empty response from payment API');
        }

        $this->success = true;

        return $this->result;
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

    public function getResult(): array
    {
        return is_array($this->result) ? $this->result : [];
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
