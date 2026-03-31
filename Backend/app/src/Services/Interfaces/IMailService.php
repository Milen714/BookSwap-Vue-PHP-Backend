<?php

namespace App\Services\Interfaces;
use App\Models\BookSwapRequest;

/**
 * IMailService
 * 
 * Mail service interface defining contract for sending email notifications
 * including user notifications, password reset emails, and request updates.
 */
interface IMailService {
    /**
     * Send email with custom subject and body
     * 
     * @param string $to Recipient email address
     * @param string $subject Email subject line
     * @param string $body Email body content
     * @return void
     */
    public function sendEmail(string $to, string $subject, string $body): void;
    
    /**
     * Send notification email to book requester
     * 
     * @param string $to Requester email address
     * @param BookSwapRequest $request Book swap request details
     * @return void
     */
    public function notifyRequester(string $to, BookSwapRequest $request): void;
    
    /**
     * Send notification email to book owner
     * 
     * @param string $to Owner email address
     * @param BookSwapRequest $request Book swap request details
     * @return void
     */
    public function notifyOwner(string $to, BookSwapRequest $request): void;
    
    /**
     * Send password reset email with reset link
     * 
     * @param string $to User email address
     * @param string $resetLink Password reset link URL
     * @return void
     */
    public function resetPasswordMail(string $to, string $resetLink): void;
}