<?php

namespace App\Services;

class MailConfigurationService
{
    public function get(): array
    {
        return [
            'mailer' => env('MAIL_MAILER'),
            'scheme' => env('MAIL_SCHEME'),
            'host' => env('MAIL_HOST'),
            'port' => env('MAIL_PORT'),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'from_address' => env('MAIL_FROM_ADDRESS'),
            'from_name' => env('MAIL_FROM_NAME'),
        ];
    }

    public function update(array $data): void
    {
        foreach ($data as $key => $value) {

            $this->setEnv(
                'MAIL_' . strtoupper($key),
                $value
            );
        }

        config([
            'mail.default' => $data['mailer'],
            'mail.mailers.smtp.scheme' => $data['scheme'],
            'mail.mailers.smtp.host' => $data['host'],
            'mail.mailers.smtp.port' => $data['port'],
            'mail.mailers.smtp.username' => $data['username'],
            'mail.mailers.smtp.password' => $data['password'],
            'mail.from.address' => $data['from_address'],
            'mail.from.name' => $data['from_name'],
        ]);
    }

    private function setEnv(string $key, string|int|null $value): void
    {
        $path = base_path('.env');

        $content = file_get_contents($path);

        $value = (string)$value;

        if (preg_match("/^{$key}=.*/m", $content)) {

            $content = preg_replace(
                "/^{$key}=.*/m",
                "{$key}=\"{$value}\"",
                $content
            );

        } else {

            $content .= PHP_EOL .
                "{$key}=\"{$value}\"";
        }

        file_put_contents($path, $content);
    }
}
