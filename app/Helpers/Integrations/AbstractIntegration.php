<?php

namespace App\Helpers\Integrations;

use App\Exceptions\IntegrationException;

abstract class AbstractIntegration
{
    protected function sendRequest(string $path, array $data = [], string $method = 'POST', array $headers = ['Content-Type: application/json', 'Cache-Control: no-cache'])
    {
        $url = trim($this->endpoint, '/') . '/' . trim($path, '/');

        $data = array_merge($this->defaultPayloadData(), $data);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        // curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgents[rand(0, count($this->userAgents) - 1)]);

        if ($method == 'GET' && !count($data)) {
            $url .= '?' . http_build_query($data);
        }

        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        curl_setopt($ch, CURLOPT_URL, $url);

        $result = curl_exec($ch);

        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) >= 500) {
            throw new IntegrationException('unexpected_service_error');
        }

        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) >= 400) {
            dump($result);
            throw new IntegrationException('unexpected_service_status');
        }

        curl_close($ch);

        $result = json_decode($result, true);

        if (json_last_error() != JSON_ERROR_NONE) {
            throw new IntegrationException('unexpected_service_response');
        }

        if ($result['success'] == false) {
            throw new IntegrationException($result['error']['message'], $result['error']['code']);
        }

        return $result['response'];
    }

    abstract protected function defaultPayloadData(): array;
}