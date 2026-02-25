<?php
namespace App\Views\Home;

?>

<section class="bg-colors text-colors py-24 mt-24">
    <div class="max-w-7xl mx-auto px-6">

        <!-- Timeline container -->
        <div class="relative w-full flex flex-col md:flex-row md:justify-between items-start">

            <!-- Vertical line (mobile) -->
            <div
                class="absolute left-[39px] -translate-x-1/2 top-0 bottom-0 w-px bg-black/70 dark:bg-white/70 md:hidden">
            </div>

            <!-- Horizontal line (desktop) -->
            <div class="absolute top-[39px] left-0 right-0 h-px bg-black/70 dark:bg-white/70 hidden md:block"></div>

            <!-- STEP 1 -->
            <div class="relative flex flex-row md:flex-col items-center w-full md:w-auto">

                <!-- Dot -->
                <div class="flex items-center justify-center
               w-[75px] h-[75px] rounded-full
               border-2 border-black/70 dark:border-white/70 bg-[#F2F0EF] dark:bg-[#0F0F0F] z-10
               shrink-0">
                    <span class="text-colors text-[40px]">1</span>
                </div>

                <!-- Content -->
                <div class="ml-6 mt-0 md:ml-0 md:mt-6
               text-left md:text-center max-w-xs">
                    <h3 class="text-xl font-semibold text-colors mb-2">
                        Browse Books
                    </h3>
                    <p class="text-black/70 dark:text-white/70">
                        Explore our extensive collection of books shared by fellow users.
                    </p>
                </div>

            </div>


            <!-- STEP 2 -->
            <div class="relative flex flex-row md:flex-col items-center w-full md:w-auto">
                <div class="flex items-center justify-center
               w-[75px] h-[75px] rounded-full
               border-2 border-black/70 dark:border-white/70 bg-[#F2F0EF] dark:bg-[#0F0F0F] z-10
               shrink-0">
                    <span class="text-colors text-[40px]">2</span>
                </div>

                <div class="ml-6 mt-0 md:ml-0 md:mt-6
               text-left md:text-center max-w-xs">
                    <h3 class="text-xl font-semibold text-colors mb-2">
                        Share Your Books
                    </h3>
                    <p class="text-black/70 dark:text-white/70">
                        Contribute to the community by sharing your own books.
                    </p>
                </div>
            </div>

            <!-- STEP 3 -->
            <div class="relative flex flex-row md:flex-col items-center w-full md:w-auto">
                <div class="flex items-center justify-center
               w-[75px] h-[75px] rounded-full
               border-2 border-black/70 dark:border-white/70 bg-[#F2F0EF] dark:bg-[#0F0F0F] z-10
               shrink-0">
                    <span class="text-colors text-[40px]">3</span>
                </div>

                <div class="ml-6 mt-0 md:ml-0 md:mt-6
               text-left md:text-center max-w-xs">
                    <h3 class="text-xl font-semibold text-colors mb-2">
                        Connect with Readers
                    </h3>
                    <p class="text-black/70 dark:text-white/70">
                        Engage with a community of book lovers and share recommendations.
                    </p>
                </div>
            </div>

        </div>

    </div>
</section>




<article class="flex flex-col flex-wrap mx-auto  p-6 rounded-md shadow-md">
    <div class="border-b-2 border-[#2C3233] mb-4">
        <h2 class="text-center m-5 text-3xl font-semibold">Browse & Search Listings</h2>
        <p class="text-center text-md font-semibold text-[#7b8186] mb-6">Find your next great read from our diverse
            collection.</p>
        <form id="book-search-form" method="GET"
            class="w-[75vw] max-w-screen-xl mx-auto md:flex md:flex-row md:flex-wrap justify-center mb-4">
            <select onchange="fetchBooksFromSearch()" id="genre-select" name="genre" class="shrink-0 text-colors bg-colors md:mb-0 mb-4
                hover-color focus:ring-4 focus:ring-neutral-tertiary
                font-medium text-sm px-4 py-2.5 focus:outline-none
                rounded-lg md:rounded-r-none md:rounded-l-lg border border-[#2C3233]
                w-full md:w-auto order-2 md:order-1 cursor-pointer">

                <option value="">All Genres</option>
                <?php
                $selectedGenre = isset($_GET['genre']) ? trim($_GET['genre'], '"') : '';
                if (isset($genres) && is_array($genres)) {
                    foreach ($genres as $genre): ?>
                <option value="<?= htmlspecialchars($genre) ?>" <?= $selectedGenre === $genre ? 'selected' : '' ?>>
                    <?= htmlspecialchars($genre) ?>
                </option>
                <?php endforeach;
                }
                ?>
            </select>
            <div class="relative flex shadow-xs rounded-base w-full md:w-auto md:flex-1 order-1 md:order-2">
                <input type="search" id="search-input" name="search" <?php if (isset($_GET['search'])): ?>
                    value="<?= htmlspecialchars($_GET['search']) ?>" <?php endif; ?> class="px-3 py-2.5 bg-colors text-colors text-sm
                    block w-full placeholder:text-body hover:bg-[#CBCBCB] dark:hover:bg-[#222222]
                    border border-[#2C3233]
                    focus:outline-none focus:ring-0 focus:border-[#2C3233]
                    rounded-l-lg md:rounded-l-none border-r-0" placeholder="Search books by Name, Author, or ISBN">
                <button type="button" onclick="fetchBooksFromSearch()" class="inline-flex items-center text-colors bg-colors hover-color
                    focus:ring-4 focus:ring-brand-medium font-medium text-sm px-4 py-2.5
                    focus:outline-none rounded-r-lg border border-[#2C3233]">
                    <svg class="w-4 h-4 me-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                        height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                            d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                    </svg>
                    Search
                </button>
            </div>
        </form>
    </div>
    <section id="booksSection" class="w-full flex flex-row flex-wrap justify-center gap-2 text-align-center">

        <?php 
        if (!isset($paginatedBooks) || empty($paginatedBooks->items)) {
            echo "<p>No books available.</p>";
        }
        foreach ($paginatedBooks->items as $book): ?>
        <?php include __DIR__ . '/../Book/BookPostComponent.php'; ?>
        <?php endforeach; ?>

        <!-- <?php 
        if (!isset($books) || empty($books)) {
            echo "<p>No books available.</p>";
        }
        foreach ($books as $book): ?>
        <?php include __DIR__ . '/../Book/BookPostComponent.php'; ?>
        <?php endforeach; ?> -->
    </section>
    <nav class="w-full flex justify-center mt-6">
        <div class="ul-wrap">
            <ul
                class="flex flex-row justify-center gap-2 bg-[#e5e5e5] dark:bg-[#222222] px-1 py-1 rounded-full whitespace-nowrap">
                <li class="hover:filter-button-active px-4 py-2">
                    <a href="/?genre=<?= htmlspecialchars($_GET['genre'] ?? '') ?>&search=<?= htmlspecialchars($_GET['search'] ?? '') ?>&page=<?php echo $paginatedBooks->currentPage == 1 ? 1 : $paginatedBooks->currentPage - 1; ?>"
                        class="text-colors font-semibold ">
                        <span>
                            < </span>
                    </a>
                </li>
                <?php
                for ($i = 1; $i <= $paginatedBooks->totalPages; $i++): ?>
                <li
                    class="<?php echo (isset($_GET['page']) && $_GET['page'] == $i) || (!isset($_GET['page']) && $i == 1) ? 'filter-button-active' : '' ?> px-4 py-2">
                    <a href="/?genre=<?= htmlspecialchars($_GET['genre'] ?? '') ?>&search=<?= htmlspecialchars($_GET['search'] ?? '') ?>&page=<?php echo $i; ?>"
                        class="text-colors font-semibold hover:underline">
                        <span>
                            <?php echo $i; ?> </span>
                    </a>
                </li>
                <?php endfor; ?>
                <li class="hover:filter-button-active px-4 py-2">
                    <a href="/?genre=<?= htmlspecialchars($_GET['genre'] ?? '') ?>&search=<?= htmlspecialchars($_GET['search'] ?? '') ?>&page=<?php echo $paginatedBooks->currentPage == $paginatedBooks->totalPages ? $paginatedBooks->currentPage : $paginatedBooks->currentPage + 1; ?>"
                        class="text-colors font-semibold ">
                        <span>
                            > </span>
                    </a>
                </li>
            </ul>
            <input id="page" type="hidden" value="<?php echo htmlspecialchars($paginatedBooks->currentPage) ?>">
        </div>
    </nav>
    <div id="Test-div"></div>
    <div id="popupOverlay" class="overflow-scroll" style="display:none;">
        <div id="popupContainer">

        </div>
    </div>

</article>
<script src="/Js/Flipcard.js"></script>
<script src="/Js/BookDetailsModal.js"></script>
<script src="/Js/BookSearch.js"></script>
<script>
document.getElementById('book-search-form').addEventListener('submit', function(e) {
    e.preventDefault();
    fetchBooksFromSearch();
});
</script>