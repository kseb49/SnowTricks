<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
/**
 * Send an email
 */
class SendEmail extends AbstractController
{

    public function __construct(private MailerInterface $mailer){}

    /**
     * Send an email
     *
     * @param string $to 
     * @param string $subject
     * @param string $template
     * @param string $token
     * @return Response
     */
    public function sendEmail(string $to, string $subject, string $template, array $context): void
    {

        $mail = (new TemplatedEmail())
            ->to($to)
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject($subject)
            ->htmlTemplate($template)
            ->context($context);
            $this->mailer->send($mail);

    }


}


