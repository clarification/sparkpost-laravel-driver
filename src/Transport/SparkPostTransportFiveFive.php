<?php

namespace Clarification\MailDrivers\Sparkpost\Transport;

use Illuminate\Mail\Transport\Transport;
use Swift_Mime_SimpleMessage;

class SparkPostTransportFiveFive extends Transport
{
    use SparkPostTransportTrait;

    public function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null)
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

        return $this->client->post($this->getEndpoint(), $options);
    }

    /**
     * Get all the addresses this message should be sent to.
     *
     * Note that SparkPost still respects CC, BCC headers in raw message itself.
     *
     * @param  Swift_Mime_SimpleMessage $message
     * @return array
     */
    protected function getRecipients(Swift_Mime_SimpleMessage $message)
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
}
