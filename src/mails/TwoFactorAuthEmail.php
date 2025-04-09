<?php

declare(strict_types = 1);

namespace Src\Mails;

use Src\Services\ConfigService;
use Src\Entities\UserLoginCode;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\BodyRendererInterface;

class TwoFactorAuthEmail
{
    public function __construct(
        private readonly ConfigService $config,
        private readonly MailerInterface $mailer,
        private readonly BodyRendererInterface $renderer
    ) {
    }

    public function send(UserLoginCode $userLoginCode): void
    {
        $email   = $userLoginCode->getUser()->getEmail();
        $message = (new TemplatedEmail())
            ->from($this->config->get('mailer.from'))
            ->to($email)
            ->subject('Blogginit Verification Code')
            ->htmlTemplate('emails/two_factor.html.twig')
            ->context(
                [
                    'user' => $userLoginCode->getUser(),
                    'code' => $userLoginCode->getCode(),
                ]
            );

        $this->renderer->render($message);

        $this->mailer->send($message);
    }
}
