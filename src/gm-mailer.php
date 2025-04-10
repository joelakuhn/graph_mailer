<?php

namespace GraphMailer;

require_once 'gm-settings.php';
require_once 'gm-provider.php';

class GMMailer {
    public static function init() {
        add_filter('wp_mail', [ self::class, 'intercept' ]);
    }

    public static function intercept($mail) {
        if (!GMProvider::is_authorized()) return $mail;
        if (!is_array($mail)) return $mail;

        $to           = self::parse_addresses($mail['to']);
        $from         = self::parse_from_address(GMSettings::get_option('from', false));
        $headers      = self::parse_headers($mail['headers']);
        $content_type = self::parse_content_type($headers['content-type'] ?? '');
        $cc           = self::parse_addresses($headers['cc'] ?? []);
        $bcc          = self::parse_addresses($headers['bcc'] ?? []);
        $reply_to     = self::parse_addresses($headers['reply-to'] ?? []);
        $subject      = $mail['subject'];
        $message      = $mail['message'];
        $attachments  = self::parse_attachments($mail['attachments']);

        $request = [
            'message' => [
                'from'          => $from,
                'toRecipients'  => $to,
                'ccRecipients'  => $cc,
                'bccRecipients' => $bcc,
                'replyTo'       => $reply_to,
                'subject'       => $subject,
                'body'          => [
                    'contentType' => $content_type,
                    'content'     => $message,
                ],
                'attachments'   => $attachments,
            ],
            'saveToSentItems' => 'true',
        ];

        GMProvider::post('/v1.0/me/sendMail', $request);

        return null;
    }

    private static function parse_headers($headers) {
        if (is_string($headers)) {
            $headers = preg_split('/\\r?\\n/', $headers);
        }
        $parsed_headers = [];
        foreach ($headers as $header) {
            $pieces = preg_split('/: */', $header);
            $parsed_headers[strtolower($pieces[0])] = strtolower($pieces[1] ?? '');
        }
        return $parsed_headers;
    }

    private static function parse_content_type($content_type) {
        $content_type = trim(strtok($content_type, ';'));
        return $content_type === 'text/html' ? 'html' : 'text';
    }

    private static function parse_addresses($addresses) {
        $addresses = is_array($addresses) ? $addresses : explode(',', $addresses);
        $addresses = array_map(fn ($addr) => trim($addr), $addresses);
        $addresses = array_filter($addresses, fn ($addr) => !empty($addr));
        $addresses = array_map(fn ($addr) => [ 'emailAddress' => [ 'address' => $addr ] ], $addresses);
        return $addresses;
    }

    private static function parse_attachments($attachments) {
        if (is_string($attachments)) $attachments = [ $attachments ];
        $graph_attachments = [];

        foreach ($attachments as $path) {
            $fh = fopen($path, 'r');
            if ($fh === false) continue;

            $fstat = fstat($fh);
            $graph_attachments[] = [
                '@odata.type' => '#microsoft.graph.fileAttachment',
                'name' => basename($path),
                'contentType' => mime_content_type($path),
                'contentBytes' => base64_encode(fread($fh, $fstat['size'])),
            ];
        }

        return $graph_attachments;
    }

    private static function parse_from_address($from) {
        if (is_string($from)) {
            return $request['message']['from'] = [ 'emailAddress' => [ 'address' => $from ] ];
        }
        return null;
    }
}

GMMailer::init();
