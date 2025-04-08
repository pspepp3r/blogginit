<?php

declare(strict_types=1);

namespace Src\Mails;

use Src\Entities\User;
use Src\Services\ConfigService;
use Src\Services\SignedUrlService;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\BodyRendererInterface;

class SignupEmail
{
    public function __construct(
        private readonly ConfigService $config,
        private readonly MailerInterface $mailer,
        private readonly BodyRendererInterface $renderer,
        private readonly SignedUrlService $signedUrl
    ) {}

    public function send(User $user): void
    {
        $email          = $user->getEmail();
        $expirationDate = new \DateTime('+30 minutes');
        $activationLink = $this->signedUrl->fromRoute(
            'verify',
            ['id' => $user->getId(), 'hash' => sha1($email)],
            $expirationDate
        );

        $message = (new TemplatedEmail())
            ->from($this->config->get('mailer.from'))
            ->to($email)
            ->subject('Welcome to Blogginit')
            ->htmlTemplate('emails/welcome.twig')
            ->context(
                [
                    'activationLink' => $activationLink,
                    'expirationDate' => $expirationDate,
                ]
            );

        $this->renderer->render($message);

        $this->mailer->send($message);
    }
}
