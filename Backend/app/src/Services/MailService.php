<?php
namespace App\Services;
use App\Services\Interfaces\IMailService;
use App\Models\Mailer;
use App\Models\BookSwapRequest;
use App\config\Secrets;

/**
 * Service responsible for outgoing email notifications.
 */
class MailService implements IMailService {
    /**
     * Send a generic HTML email using configured transport.
     *
     * @param string $to Recipient email address.
     * @param string $subject Email subject line.
     * @param string $body HTML email body.
     * @return void
     */
    public function sendEmail(string $to, string $subject, string $body): void {
        $mailConfig = require __DIR__ . '/../../config/mailConfig.php';
        $mailer = new Mailer($mailConfig);
        $mailer->send($to, $body, $subject);
    }

    /**
     * Notify the requester after payment/processing is completed.
     *
     * @param string $to Requester email address.
     * @param BookSwapRequest $request Request context.
     * @return void
     */
    public function notifyRequester(string $to, BookSwapRequest $request): void {
        $subject = "BookSwap Notification";
        $link = Secrets::$domain . "/myRequest?requestId=" . urlencode($request->id). "&requesterId=" . urlencode($request->requester->id). "&requesterToken=" . urlencode($request->requester_action_token);
        $message = "<h1>Your book request  for " . htmlspecialchars($request->book->title) . " has been processed successfully.</h1>
                    <p>We are pleased to inform you that your request is now being processed.</p>
                    <p>The owner will ship the book to you shortly.</p>
                    <p>View your request <a href='" . htmlspecialchars($link) . "'>here</a>.</p>
                    <p>Thank you for using BookSwap!</p>";
        $this->sendEmail($to, $subject, $message);
    }

    /**
     * Notify the book owner that shipping has been paid.
     *
     * @param string $to Owner email address.
     * @param BookSwapRequest $request Request context.
     * @return void
     */
    public function notifyOwner(string $to, BookSwapRequest $request): void {
        $subject = "Shipping For Your BookSwap Listing Has Been paid";
        $link = Secrets::$domain . "/myListings?id=" . urlencode($request->owner->id);
        $message = "<h1>Your book " . htmlspecialchars($request->book->title) . " has been requested.</h1>
                    <p>We are pleased to inform you that your book has been requested by " . htmlspecialchars($request->requester->fname) . " " . htmlspecialchars($request->requester->lname) . ".</p>
                    <p>Please prepare the book for shipping.</p>
                    <p>View your listings <a href='" . htmlspecialchars($link) . "'>here</a>.</p>
                    <p>Thank you for using BookSwap!</p>";
        $this->sendEmail($to, $subject, $message);
    }

    /**
     * Send password reset email with one-time reset link.
     *
     * @param string $to Recipient email address.
     * @param string $resetLink Absolute reset URL.
     * @return void
     */
    public function resetPasswordMail(string $to, string $resetLink): void {
        $subject = "BookSwap Password Reset Request";
        $body = "<h1>Password Reset Request</h1>
                 <p>Click the link below to reset your password:</p>
                 <a href='" . htmlspecialchars($resetLink) . "'>Reset Password</a>
                 <p>This link will expire in 1 hour.</p>";
        $this->sendEmail($to, $subject, $body);
    }
}