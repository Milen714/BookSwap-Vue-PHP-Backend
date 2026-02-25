<?php
namespace App\Views\BookRequest;
use App\Models\BookSwapStatus;
?>

<?php for ($i = 0; $i < count($bookRequests); $i++): ?>
<?php $request = $bookRequests[$i]; ?>
<?php if($i % 2 == 0): ?>
<?php $flexreverse = "md:flex-row-reverse items-start"; ?>
<?php else: ?>
<?php $flexreverse = "md:flex-row items-start"; ?>
<?php endif; ?>

<?php 
// Prepare progress bar steps
        $steps = [];
        foreach (BookSwapStatus::cases() as $status) {
            $steps[] = $status->value;
        }
        
        $stepCompletedStyles = "completed flex flex-col items-center gap-2 text-colors";
        $completedImage = "/Assets/Checkmark.svg";
        $stepIncompleteStyles = "incomplete flex flex-col items-center gap-2 text-[#888] dark:text-gray-500";
        $incompleteImage = "/Assets/NeutralCircle.svg";

        $currentStatusIndex = array_search($request->status->value, $steps);
        
        
        $stepStyles = [];
        $stepImages = [];
        for($j = 0; $j < count($steps); $j++){
            if($j <= $currentStatusIndex - 1){
                $stepStyles[$j] = $stepCompletedStyles;
                $stepImages[$j] = $completedImage;
            } else {
                $stepStyles[$j] = $stepIncompleteStyles;
                $stepImages[$j] = $incompleteImage;
            }
        }
         ?>

<div
    class="book-container flex flex-col items-center <?php echo $flexreverse; ?> gap-6  w-full max-w-3xl bg-colors border border-[#ccc] dark:border-[#2C3233] rounded-xl p-4">
    <div class="w-min">
        <?php
                        $book = $request->book;
                        include __DIR__ . '/../Book/BookPostComponent.php'; ?>
    </div>
    <div class="flex flex-col items-center">
        <div>
            <?php
            $isOwner = isset($_SESSION['loggedInUser']) && $_SESSION['loggedInUser']->id === $request->owner->id;
            
            if ($request->status === BookSwapStatus::PENDING): ?>
            <?php if ($isOwner): ?>
            <h2 class="text-2xl font-bold mb-2 text-colors">Listed for Swap</h2>
            <p class="text-[#555] dark:text-gray-400 mb-4">Your book is available. Waiting for someone to request it.
            </p>
            <?php else: ?>
            <h2 class="text-2xl font-bold mb-2 text-colors">Pay for Shipping</h2>
            <p class="text-[#555] dark:text-gray-400 mb-4">Complete payment to proceed with your book swap request.</p>
            <?php endif; ?>

            <?php elseif ($request->status === BookSwapStatus::SHIPPINGPAID): ?>
            <?php if ($isOwner): ?>
            <h2 class="text-2xl font-bold mb-2 text-colors">Shipping Paid</h2>
            <p class="text-[#555] dark:text-gray-400 mb-4">Download your shipping label and ship the book.</p>
            <?php else: ?>
            <h2 class="text-2xl font-bold mb-2 text-colors">Shipping Paid</h2>
            <p class="text-[#555] dark:text-gray-400 mb-4">Waiting for <?= htmlspecialchars($request->owner->fname) ?>
                to ship your book.
            </p>
            <?php endif; ?>

            <?php elseif ($request->status === BookSwapStatus::SHIPPED): ?>
            <?php if ($isOwner): ?>
            <h2 class="text-2xl font-bold mb-2 text-colors">Book Shipped</h2>
            <p class="text-[#555] dark:text-gray-400 mb-4">Your book is on its way to
                <?= htmlspecialchars($request->requester->fname) ?>.</p>
            <?php else: ?>
            <h2 class="text-2xl font-bold mb-2 text-colors">Book Shipped</h2>
            <p class="text-[#555] dark:text-gray-400 mb-4">Your book is on its way! Confirm delivery when it arrives.
            </p>
            <?php endif; ?>

            <?php elseif ($request->status === BookSwapStatus::DELIVERED): ?>
            <?php if ($isOwner): ?>
            <h2 class="text-2xl font-bold mb-2 text-colors">Book Delivered</h2>
            <p class="text-[#555] dark:text-gray-400 mb-4"><?= htmlspecialchars($request->requester->fname) ?> has
                received the book.</p>
            <?php else: ?>
            <h2 class="text-2xl font-bold mb-2 text-colors">Book Delivered</h2>
            <p class="text-[#555] dark:text-gray-400 mb-4">Mark as completed to finish the swap.</p>
            <?php endif; ?>

            <?php elseif ($request->status === BookSwapStatus::COMPLETED): ?>
            <h2 class="text-2xl font-bold mb-2 text-colors">Swap Completed</h2>
            <p class="text-[#555] dark:text-gray-400 mb-4">This book swap has been successfully completed.</p>

            <?php elseif ($request->status === BookSwapStatus::TAKENDOWN): ?>
            <h2 class="text-2xl font-bold mb-2 text-colors">Post Taken Down</h2>
            <p class="text-[#555] dark:text-gray-400 mb-4">This book swap post has been taken down.</p>
            <?php endif; ?>


            <div class="request-progress-bar">
                <div class="flex flex-row items-center justify-center gap-8">
                    <?php if ($request->status !== BookSwapStatus::TAKENDOWN): ?>
                    <div class="step <?php echo $stepStyles[0]; ?>">
                        <div class="step-number"><img src="<?php echo $stepImages[0]; ?>" alt=""></div>
                        <div class="step-label">Shipping Paid</div>
                    </div>
                    <div class="step <?php echo $stepStyles[1]; ?>">
                        <div class="step-number"><img src="<?php echo $stepImages[1]; ?>" alt=""></div>
                        <div class="step-label">Shipped</div>
                    </div>
                    <div class="step <?php echo $stepStyles[2]; ?>">
                        <div class="step-number"><img src="<?php echo $stepImages[2]; ?>" alt=""></div>
                        <div class="step-label">Delivered</div>
                    </div>
                    <div class="step <?php echo $stepStyles[3]; ?>">
                        <div class="step-number"><img src="<?php echo $stepImages[3]; ?>" alt=""></div>
                        <div class="step-label">Completed</div>
                    </div>
                    <?php elseif ($request->status === BookSwapStatus::TAKENDOWN): ?>
                    <div class="step completed flex flex-col items-center gap-2 text-[#888] dark:text-gray-500">
                        <div class="step-number"><img src="/Assets/Cross.svg" alt=""></div>
                        <div class="step-label">Taken Down</div>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="buttons flex flex-wrap gap-3 pt-2 border-t border-[#ccc] dark:border-[#2C3233] mt-2">
                    <?php if($request->status === BookSwapStatus::PENDING): ?>
                    <?php if ($isOwner): ?>
                    <button type="button"
                        class="inline-flex items-center justify-center rounded-lg border border-red-500/60 bg-red-500/10 px-4 py-2 text-sm font-semibold text-red-300 hover:bg-red-500/20 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1 focus:ring-offset-[#0F0F0F]"
                        data-request-id="<?= htmlspecialchars($request->id) ?>"
                        data-next-status="<?= BookSwapStatus::TAKENDOWN->value ?>">
                        Take down post
                    </button>
                    <?php else: ?>
                    <button type="button" onclick="openBookDetails(<?= htmlspecialchars($request->book->id) ?>, true)"
                        class="inline-flex items-center justify-center rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-1 focus:ring-offset-[#0F0F0F]">
                        Pay for Shipping
                    </button>
                    <button type="button"
                        class="inline-flex items-center justify-center rounded-lg border border-red-500/60 bg-red-500/10 px-4 py-2 text-sm font-semibold text-red-300 hover:bg-red-500/20 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1 focus:ring-offset-[#0F0F0F]"
                        data-request-id="<?= htmlspecialchars($request->id) ?>">
                        Cancel request
                    </button>
                    <?php endif; ?>

                    <?php elseif($request->status === BookSwapStatus::SHIPPINGPAID): ?>
                    <?php if ($isOwner): ?>
                    <a href="/shipping-label/<?= htmlspecialchars($request->id) ?>"
                        class="inline-flex items-center justify-center rounded-lg bg-cyan-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-cyan-500 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-1 focus:ring-offset-[#0F0F0F]">
                        Download Label
                    </a>
                    <button type="button"
                        class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 focus:ring-offset-[#0F0F0F]"
                        data-request-id="<?= htmlspecialchars($request->id) ?>"
                        data-next-status="<?= BookSwapStatus::SHIPPED->value ?>">
                        Mark as shipped
                    </button>
                    <?php else: ?>
                    <span
                        class="inline-flex items-center rounded-full bg-blue-500/20 border border-blue-500/40 px-4 py-2 text-sm font-semibold text-blue-300">
                        Waiting for shipment...
                    </span>
                    <button type="button"
                        class="inline-flex items-center justify-center rounded-lg border border-red-500/60 bg-red-500/10 px-4 py-2 text-sm font-semibold text-red-300 hover:bg-red-500/20 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1 focus:ring-offset-[#0F0F0F]"
                        data-request-id="<?= htmlspecialchars($request->id) ?>">
                        Cancel request
                    </button>
                    <?php endif; ?>

                    <?php elseif($request->status === BookSwapStatus::SHIPPED): ?>
                    <?php if ($isOwner): ?>
                    <span
                        class="inline-flex items-center rounded-full bg-blue-100 dark:bg-blue-500/20 border border-blue-400 dark:border-blue-500/40 px-4 py-2 text-sm font-semibold text-blue-700 dark:text-blue-300">
                        In transit...
                    </span>
                    <?php else: ?>
                    <button type="button"
                        class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-1 focus:ring-offset-[#0F0F0F]"
                        data-request-id="<?= htmlspecialchars($request->id) ?>"
                        data-next-status="<?= BookSwapStatus::DELIVERED->value ?>">
                        Confirm delivery
                    </button>
                    <button type="button"
                        class="inline-flex items-center justify-center rounded-lg border border-red-500/60 bg-red-500/10 px-4 py-2 text-sm font-semibold text-red-300 hover:bg-red-500/20 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1 focus:ring-offset-[#0F0F0F]"
                        data-request-id="<?= htmlspecialchars($request->id) ?>">
                        Report issue
                    </button>
                    <?php endif; ?>

                    <?php elseif($request->status === BookSwapStatus::DELIVERED): ?>
                    <?php if ($isOwner): ?>
                    <span
                        class="inline-flex items-center rounded-full bg-emerald-100 dark:bg-emerald-500/20 border border-emerald-400 dark:border-emerald-500/40 px-4 py-2 text-sm font-semibold text-emerald-700 dark:text-emerald-300">
                        Delivered successfully
                    </span>
                    <?php else: ?>
                    <button type="button"
                        class="inline-flex items-center justify-center rounded-lg bg-purple-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-1 focus:ring-offset-[#0F0F0F]"
                        data-request-id="<?= htmlspecialchars($request->id) ?>"
                        data-next-status="<?= BookSwapStatus::COMPLETED->value ?>">
                        Mark as completed
                    </button>
                    <?php endif; ?>

                    <?php elseif($request->status === BookSwapStatus::COMPLETED): ?>
                    <span
                        class="inline-flex items-center rounded-full bg-emerald-100 dark:bg-emerald-500/20 border border-emerald-400 dark:border-emerald-500/40 px-4 py-2 text-sm font-semibold text-emerald-700 dark:text-emerald-300">
                        ✓ Swap Completed
                    </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endfor; ?>