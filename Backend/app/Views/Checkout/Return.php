<?php
// $sessionId, $session, and $isPaid are passed from the controller
?>

<div class="min-h-[80vh] flex items-center justify-center px-4 py-12">
    <div class="max-w-md w-full">
        <!-- Card Container -->
        <div class="bg-neutral-secondary-soft border border-default rounded-2xl p-8 text-center shadow-xl">

            <?php if ($isPaid): ?>
            <!-- Success State -->
            <div class="mb-6">
                <div class="w-20 h-20 mx-auto bg-green-500/20 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-heading mb-2">Payment Successful!</h1>
                <p class="text-body">Your transaction has been completed successfully.</p>
            </div>

            <!-- Order Details -->
            <div class="bg-neutral-primary rounded-xl p-4 mb-6 text-left">
                <div class="flex justify-between items-center py-2 border-b border-default">
                    <span class="text-body-muted text-sm">Status</span>
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-500/20 text-green-400">
                        Paid
                    </span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-default">
                    <span class="text-body-muted text-sm">Amount</span>
                    <span class="text-heading font-medium">€<?= number_format($session->amount_total / 100, 2) ?></span>
                </div>
                <div class="flex justify-between items-center py-2">
                    <span class="text-body-muted text-sm">Transaction ID</span>
                    <span
                        class="text-body text-xs font-mono"><?= htmlspecialchars(substr($sessionId, 0, 20)) ?>...</span>
                </div>
            </div>

            <!-- What's Next -->
            <div class="bg-blue-500/10 border border-blue-500/20 rounded-xl p-4 mb-6 text-left">
                <h3 class="text-blue-400 font-medium mb-2 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    What happens next?
                </h3>
                <ul class="text-sm text-body space-y-1">
                    <li>• The book owner has been notified</li>
                    <li>• Your book will be shipped soon</li>
                    <li>• You'll receive tracking information via email</li>
                </ul>
            </div>

            <?php else: ?>
            <!-- Failed State -->
            <div class="mb-6">
                <div class="w-20 h-20 mx-auto bg-red-500/20 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-heading mb-2">Payment Not Completed</h1>
                <p class="text-body">Something went wrong with your payment. Please try again.</p>
            </div>

            <!-- Error Details -->
            <div class="bg-neutral-primary rounded-xl p-4 mb-6 text-left">
                <div class="flex justify-between items-center py-2">
                    <span class="text-body-muted text-sm">Status</span>
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-500/20 text-red-400">
                        <?= htmlspecialchars(ucfirst($session->payment_status)) ?>
                    </span>
                </div>
            </div>

            <a href="/checkout?requestId=<?= htmlspecialchars($_GET['requestId'] ?? '') ?>"
                class="inline-flex items-center justify-center gap-2 w-full bg-brand hover:bg-brand-hover text-white font-medium py-3 px-6 rounded-xl transition-colors mb-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                    </path>
                </svg>
                Try Again
            </a>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="/"
                    class="flex-1 inline-flex items-center justify-center gap-2 bg-neutral-primary hover:bg-neutral-secondary border border-default text-heading font-medium py-3 px-6 rounded-xl transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                        </path>
                    </svg>
                    Back to Home
                </a>
                <?php if ($isPaid): ?>
                <a href="/myRequests"
                    class="flex-1 inline-flex items-center justify-center gap-2 bg-brand hover:bg-brand-hover text-white font-medium py-3 px-6 rounded-xl transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                        </path>
                    </svg>
                    View My Requests
                </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Footer Note -->
        <p class="text-center text-body-muted text-sm mt-6">
            Questions? <a href="/contact" class="text-fg-brand hover:underline">Contact Support</a>
        </p>
    </div>
</div>