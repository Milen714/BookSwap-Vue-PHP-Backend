<?php
namespace App\Services;
use App\Services\Interfaces\IMailService;
use App\Models\Mailer;
use App\Models\BookSwapRequest;
class MailService implements IMailService {
    public function sendEmail(string $to, string $subject, string $body): void {
        $mailConfig = require __DIR__ . '/../../config/mailConfig.php';
        $mailer = new Mailer($mailConfig);
        $mailer->send($to, $body, $subject);
    }
    public function notifyRequester(string $to, BookSwapRequest $request): void {
        require_once '../config/secrets.php';
        $subject = "BookSwap Notification";
        $link = $DOMAIN_URL . "/myRequest?requestId=" . urlencode($request->id). "&requesterId=" . urlencode($request->requester->id). "&requesterToken=" . urlencode($request->requester_action_token);
        $message = "<h1>Your book request  for " . htmlspecialchars($request->book->title) . " has been processed successfully.</h1>
                    <p>We are pleased to inform you that your request is now being processed.</p>
                    <p>The owner will ship the book to you shortly.</p>
                    <p>View your request <a href='" . htmlspecialchars($link) . "'>here</a>.</p>
                    <p>Thank you for using BookSwap!</p>";
        $this->sendEmail($to, $subject, $message);
    }
    public function notifyOwner(string $to, BookSwapRequest $request): void {
            require_once '../config/secrets.php';
        $subject = "Shipping For Your BookSwap Listing Has Been paid";
        $link = $DOMAIN_URL . "/myListings?id=" . urlencode($request->owner->id);
        $message = "<h1>Your book " . htmlspecialchars($request->book->title) . " has been requested.</h1>
                    <p>We are pleased to inform you that your book has been requested by " . htmlspecialchars($request->requester->fname) . " " . htmlspecialchars($request->requester->lname) . ".</p>
                    <p>Please prepare the book for shipping.</p>
                    <p>View your listings <a href='" . htmlspecialchars($link) . "'>here</a>.</p>
                    <p>Thank you for using BookSwap!</p>";
        $this->sendEmail($to, $subject, $message);
    }
    public function resetPasswordMail(string $to, string $resetLink): void {
        $subject = "BookSwap Password Reset Request";
        $body = "<h1>Password Reset Request</h1>
                 <p>Click the link below to reset your password:</p>
                 <a href='" . htmlspecialchars($resetLink) . "'>Reset Password</a>
                 <p>This link will expire in 1 hour.</p>";
        $this->sendEmail($to, $subject, $body);
    }
}