<?php 
namespace App\Views\BookRequest;
use App\Models\BookSwapStatus;
$activeStyle = "bg-[#d5d5d5] dark:bg-[#0F0F0F] rounded-full";
$loggedInId = isset($_SESSION['loggedInUser']) ? $_SESSION['loggedInUser']->id : null;
?>
<div class="container mx-auto px-4 py-8 flex flex-col items-center justify-center">
    <header class="flex flex-col gap-4 items-center m-8">
        <h1 class="text-3xl font-bold mb-6 text-colors">My Book Swap Requests</h1>
        <!-- filter options here -->
        <ul
            class="flex flex-row justify-center gap-2 bg-[#e5e5e5] dark:bg-[#222222] px-1 py-1 rounded-full whitespace-nowrap">
            <li
                class="<?php echo $_SERVER['QUERY_STRING'] === 'status=all' || empty($_SERVER['QUERY_STRING']) ? $activeStyle : '' ?> px-4 py-2">
                <a href="/myRequests<?php echo '/' . htmlspecialchars($loggedInId); ?>?status=all"
                    class="text-colors font-semibold hover:underline">All</a>
            </li>
            <li class="<?php echo $_SERVER['QUERY_STRING'] === 'status=inProgress' ? $activeStyle : '' ?> px-4 py-2"><a
                    href="/myRequests<?php echo '/' . htmlspecialchars($loggedInId); ?>?status=inProgress"
                    class="text-colors font-semibold hover:underline">In Progress</a>
            </li>
            <li class="<?php echo $_SERVER['QUERY_STRING'] === 'status=completed' ? $activeStyle : '' ?> px-4 py-2"><a
                    href="/myRequests<?php echo '/' . htmlspecialchars($loggedInId); ?>?status=completed"
                    class="text-colors font-semibold hover:underline">Completed
                </a></li>
        </ul>
    </header>
    <?php if (empty($bookRequests)): ?>
    <p class="text-lg text-colors">You have no book swap requests.</p>
    <?php else: ?>

    <section class="requests_ontainer flex flex-col gap-6">


        <?php include __DIR__ . '/RequestCardComponent.php'; ?>

    </section>
    <?php endif; ?>
</div>
<div id="popupOverlay" class="overflow-scroll" style="display:none;">
    <div id="popupContainer">

    </div>
</div>
<script src="/Js/updateRequestStatus.js"></script>
<script src="/Js/Flipcard.js"></script>
<script src="/Js/BookDetailsModal.js"></script>