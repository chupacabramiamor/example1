<?php

namespace App\Helpers\Transports;

use Illuminate\Mail\Transport\Transport;
use Sendpulse\RestApi\ApiClient;
use Sendpulse\RestApi\Storage\FileStorage;

class SendpulseTransport extends Transport
{
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function send(\Swift_Mime_SimpleMessage $message, &$failedRecipients = null)
    {
        $recipients = [];

        foreach ($message->getTo() as $email => $name) {
            $recipients[] = compact('email', 'name');
        }

        $data = [
            'html' => $message->getBody(),
            'subject' => $message->getSubject(),
            'from' => [
                'name' => $this->config['from']['name'],
                'email' => $this->config['from']['address']
            ],
            'to' => $recipients,
        ];

        $client = new ApiClient($this->config['id'], $this->config['secret'], new FileStorage('/tmp/'));

        $result = $client->smtpSendMail($data);

        $resultCode = 0;

        return isset($result->id) ? 0 : $result->error_code;
    }
}
