<?php

namespace Clarification\MailDrivers\Sparkpost\Transport;

use Swift_Mime_Message;
use GuzzleHttp\ClientInterface;

trait SparkPostTransportTrait
{
    /**
     * Guzzle client instance.
     *
     * @var ClientInterface
     */
    protected $client;

    /**
     * The SparkPost API key.
     *
     * @var string
     */
    protected $key;

    /**
     * Create a new SparkPost transport instance.
     *
     * @param  ClientInterface $client
     * @param  string $key
     * @param  array $options
     */
    public function __construct(ClientInterface $client, $key, $options = [])
    {
        $this->client = $client;
        $this->key = $key;
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function send(Swift_Mime_Message $message, &$failedRecipients = null)
    {
        $this->beforeSendPerformed($message);

        $recipients = $this->getRecipients($message);

        $message->setBcc([]);

        $options = [
            'headers' => [
                'Authorization' => $this->key,
            ],
            'json' => [
                'recipients' => $recipients,
                'content' => [
                    'email_rfc822' => $message->toString(),
                ],
            ],
        ];

        if ($this->options) {
            $options['json']['options'] = $this->options;
        }

        return $this->client->post('https://api.sparkpost.com/api/v1/transmissions', $options);
    }

    /**
     * Get all the addresses this message should be sent to.
     *
     * Note that SparkPost still respects CC, BCC headers in raw message itself.
     *
     * @param  Swift_Mime_Message $message
     * @return array
     */
    protected function getRecipients(Swift_Mime_Message $message)
    {
        $to = [];
        if ($getTo = $message->getTo()) {
            $to = array_merge($to, array_keys($getTo));
        }

        if ($getCc = $message->getCc()) {
            $to = array_merge($to, array_keys($getCc));
        }

        if ($getBcc = $message->getBcc()) {
            $to = array_merge($to, array_keys($getBcc));
        }

        $recipients = array_map(function ($address) {
            return compact('address');
        }, $to);

        return $recipients;
    }

    /**
     * Get the API key being used by the transport.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set the API key being used by the transport.
     *
     * @param  string  $key
     * @return string
     */
    public function setKey($key)
    {
        return $this->key = $key;
    }
}
