<?php 
namespace App\Views\BookRequest;
$activeStyle = "bg-[#d5d5d5] dark:bg-[#0F0F0F] rounded-full";
$loggedInId = isset($_SESSION['loggedInUser']) ? $_SESSION['loggedInUser']->id : null;
?>
<div class="container mx-auto px-4 py-8 flex flex-col items-center justify-center">
    <header class="flex flex-col gap-4 items-center m-8">
        <h1 class="text-3xl font-bold mb-6 text-colors">My Book Swap Requests</h1>
        <!-- filter options here -->
        <ul class="flex flex-row justify-center  w-min gap-2 bg-[#e5e5e5] dark:bg-[#222222] px-1 py-1 rounded-full">
            <li
                class="<?php echo $_SERVER['QUERY_STRING'] === 'status=all' || empty($_SERVER['QUERY_STRING']) ? $activeStyle : '' ?> px-4 py-2">
                <a href="/myListings<?php echo '/' . $loggedInId; ?>?status=all"
                    class="text-colors font-semibold hover:underline">All</a>
            </li>
            <li class="<?php echo $_SERVER['QUERY_STRING'] === 'status=listed' ? $activeStyle : '' ?> px-4 py-2"><a
                    href="/myListings<?php echo '/' . $loggedInId; ?>?status=listed"
                    class="text-colors font-semibold hover:underline">Listed</a>
            </li>
            <li class="<?php echo $_SERVER['QUERY_STRING'] === 'status=completed' ? $activeStyle : '' ?> px-4 py-2"><a
                    href="/myListings<?php echo '/' . $loggedInId; ?>?status=completed"
                    class="text-colors font-semibold hover:underline">Completed
                </a></li>
            <li class="<?php echo $_SERVER['QUERY_STRING'] === 'status=takenDown' ? $activeStyle : '' ?> px-4 py-2"><a
                    href="/myListings<?php echo '/' . $loggedInId; ?>?status=takenDown"
                    class="text-colors font-semibold hover:underline">Takendown
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