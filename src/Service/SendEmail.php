<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
/**
 * Send an email
 */
class SendEmail extends AbstractController
{


    public function __construct(private MailerInterface $mailer)
    {

    }


    /**
     * Send an email
     *
     * @param string $to       The recipient adress
     * @param string $subject  The subject
     * @param string $template The template of the email
     * @param array  $context  The variables needed
     * @return Response
     */
    public function sendEmail(string $to, string $subject, string $template, array $context): void
    {

        $mail = (new TemplatedEmail())
            ->to($to)
            // ->replyTo('fabien@example.com')
            // ->priority(Email::PRIORITY_HIGH)
            ->subject($subject)
            ->htmlTemplate($template)
            ->context($context);
            $this->mailer->send($mail);

    }


}
