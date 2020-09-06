<?php

namespace App\Helpers\Integrations\Browserless;

use App\Helpers\Integrations\AbstractIntegration;
use App\Helpers\Integrations\WebsiteScrappingInterface;
use Symfony\Component\HttpFoundation\File\File;

class Manager extends AbstractIntegration implements WebsiteScrappingInterface
{
    private $token;
    protected $endpoint = 'https://chrome.browserless.io/';

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function screenshot(string $origin): File
    {
        $contents = $this->sendRequest('/screenshot?token=' . $this->token, [
            'url' => $origin,
            'options' => [
                'fullPage' => true,
                'type' => 'png',
            ]
        ]);

        $tmpName = tempnam(sys_get_temp_dir(), 'VIVA_');
        file_put_contents($tmpName, $contents);

        return new File($tmpName);
    }

    protected function defaultPayloadData() : array
    {
        return [];
    }

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
            throw new IntegrationException('unexpected_service_status');
        }

        curl_close($ch);

        return $result;
    }
}