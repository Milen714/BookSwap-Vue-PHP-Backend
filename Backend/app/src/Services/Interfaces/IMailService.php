<?php

namespace App\Services\Interfaces;
use App\Models\BookSwapRequest;
interface IMailService {
    public function sendEmail(string $to, string $subject, string $body): void;
    public function notifyRequester(string $to, BookSwapRequest $request): void;
    public function notifyOwner(string $to, BookSwapRequest $request): void;
    public function resetPasswordMail(string $to, string $resetLink): void;
}