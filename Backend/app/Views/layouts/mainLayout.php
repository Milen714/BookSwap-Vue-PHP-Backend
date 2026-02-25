<?php
$theme = $_COOKIE['theme'] ?? 'light';
$darkClass = $theme === 'dark' ? 'dark' : '';
?>
<!DOCTYPE html>
<html lang="en" class="<?php echo $darkClass ?>">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo $title ?? 'My App'; ?></title>
    <link rel="icon" type="image/svg+xml" href="/Assets/Favicon.svg">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/custom.css">
    <script src="https://js.stripe.com/v3/"></script>

</head>
<!-- Toast Container -->
<div id="toast-root" class="
    fixed z-[9999]
    top-2 left-2 right-2
    md:right-auto md:w-[380px]
    flex flex-col gap-2
    pointer-events-none
  "></div>

<body class="bg-colors text-colors">
    <!-- Navbar -->
    <nav class="bg-colors fixed w-full z-20 top-0 start-0 border-b border-[#2C3233]">

        <div class="nav-ul-container  max-w-screen-xl flex flex-wrap  justify-between mx-auto p-4">
            <!-- Logo and Brand Name -->
            <a href="/" class="flex items-center space-x-3 rtl:space-x-reverse">
                <img src="/Assets/BookSwapLogo.svg" class="h-7" alt="BookSwap Logo" />
                <span class="self-center text-xl text-heading font-semibold whitespace-nowrap">BookSwap</span>
            </a>
            <!-- Mobile menu button -->
            <button data-collapse-toggle="navbar-multi-level-dropdown" type="button"
                class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-body rounded-base md:hidden hover:bg-neutral-secondary-soft hover:text-heading focus:outline-none focus:ring-2 focus:ring-neutral-tertiary"
                aria-controls="navbar-multi-level-dropdown" aria-expanded="false">
                <span class="sr-only">Open main menu</span>
                <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                    fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M5 7h14M5 12h14M5 17h14" />
                </svg>
            </button>
            <!-- Navigation links -->
            <div class="hidden w-full md:block md:w-auto" id="navbar-multi-level-dropdown">
                <ul
                    class="flex flex-col items-center font-medium p-4 md:p-0 mt-4 rounded-lg border border-[#2C3233] bg-colors shadow-lg md:space-x-8 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0 md:bg-transparent md:shadow-none">
                    <?php
                    if(!isset($_SESSION['loggedInUser'])): ?>
                    <li>
                        <?php include __DIR__ . '/../Shared/ThemeToggleButton.php'; ?>
                    </li>
                    <li><a class="block py-2 px-3 <?php echo str_contains($_SERVER['REQUEST_URI'], '/login') ? 'text-blue-600 ' : 'text-colors' ?> rounded md:bg-transparent md:p-0"
                            href="/login">Login</a></li>
                    <li><a class="block py-2 px-3 <?php echo str_contains($_SERVER['REQUEST_URI'], '/signup') ? 'text-blue-600 ' : 'text-colors' ?> rounded md:bg-transparent md:p-0"
                            href="/signup">Signup</a></li>
                    <?php else: ?>
                    <li>
                        <a href="/"
                            class="block py-2 px-3  <?php echo $_SERVER['REQUEST_URI'] == '/' ? 'text-blue-600 ' : 'text-colors' ?> rounded md:bg-transparent md:p-0"
                            aria-current="page">Browse</a>
                    </li>

                    <li>
                        <a href="/addBook"
                            class="block py-2 <?php echo $_SERVER['REQUEST_URI'] == '/addBook' ? 'text-blue-600 ' : 'text-colors' ?> px-3 rounded hover-color md:hover:bg-transparent md:border-0 md:p-0">
                            List a Book</a>
                    </li>
                    <li>
                        <a href="/myListings/<?php echo isset($_SESSION['loggedInUser']) ? $_SESSION['loggedInUser']->id : '' ; ?>"
                            class="block py-2 <?php echo str_contains($_SERVER['REQUEST_URI'], '/myListings') ? 'text-blue-600 ' : 'text-colors' ?> px-3 rounded hover-color md:hover:bg-transparent md:border-0 md:p-0">
                            My Listings</a>
                    </li>
                    <li>
                        <a href="/myRequests/<?php echo isset($_SESSION['loggedInUser']) ? $_SESSION['loggedInUser']->id : '' ; ?>"
                            class="block py-2 <?php echo str_contains($_SERVER['REQUEST_URI'], '/myRequests') ? 'text-blue-600 ' : 'text-colors' ?> px-3 rounded hover-color md:hover:bg-transparent md:border-0 md:p-0">
                            My Requests</a>
                    </li>
                    <li>

                        <?php include __DIR__ . '/../Shared/ThemeToggleButton.php'; ?>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
            <!-- Search form -->
            <!-- User menu button -->
            <?php if(isset($_SESSION['loggedInUser'])): ?>
            <div class="relative flex flex-row gap-2 items-center">
                <button id="userMenuButton" type="button" aria-expanded="false"
                    class="inline-flex items-center text-colors font-semibold p-2 rounded-full
                   bg-[#CBCBCB] dark:bg-[#222222] hover:bg-[#b5b5b5] dark:hover:bg-[#3a3a3a] focus:outline-none focus:ring-2 focus:ring-brand">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-muted">
                        <?= strtoupper(substr($_SESSION['loggedInUser']->fname, 0, 1) . substr($_SESSION['loggedInUser']->lname, 0, 1)); ?>
                    </span>
                    <span class="sr-only">Toggle user menu</span>
                </button>
                <!-- Dropdown -->
                <div id="userMenuDropdown" class="absolute right-0 top-full mt-2 w-44 z-50 hidden
                   rounded-lg border border-[#2C3233]
                   bg-[#F2F0EF] dark:bg-[#0F0F0F] shadow-lg">
                    <ul class="py-1 text-sm text-black dark:text-white" aria-labelledby="userMenuButton">
                        <li>
                            <a href="/settings" class="block px-4 py-2 hover-color rounded-md">
                                Settings
                            </a>
                        </li>
                        <li>
                            <form action="/logout" method="post">
                                <button type="submit" class="w-full text-left px-4 py-2 hover-color rounded-md">
                                    Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
                <span class="text-colors font-medium whitespace-nowrap">
                    <span id="userTokens"></span> Credits
                </span>
            </div>
            <?php endif; ?>
        </div>

    </nav>


    <main class="p-4 bg-colors mt-36 min-h-[75vh]">
        <?php echo $content; ?>
    </main>

    <footer class="p-4 bg-colors border-t border-[#2C3233] text-center">
        <p>&copy; 2025 My App <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="icon size-6 ">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M15.182 15.182a4.5 4.5 0 0 1-6.364 0M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z" />
            </svg>
        </p>
    </footer>

    <?php if($_SERVER['REQUEST_URI'] === '/' || str_contains($_SERVER['REQUEST_URI'], '?genre')): ?>
    <script src="/Js/BookSearch.js"></script>
    <?php endif; ?>
    <script src="/Js/toast.js"></script>
    <?php if (isset($_SESSION['toast'])): ?>
    <script>
    toast(<?= json_encode($_SESSION['toast']) ?>);
    </script>
    <?php unset($_SESSION['toast']); endif; ?>

    <script src="/Js/Navbar.js"></script>
    <?php if (isset($_SESSION['loggedInUser'])): ?>
    <script src="/Js/userTokens.js"></script>
    <?php endif; ?>
    <script>
    // Theme toggle functionality
    const themeToggle = document.getElementById('themeToggle');

    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            document.documentElement.classList.toggle('dark');
            const newTheme = document.documentElement.classList.contains('dark') ? 'dark' : 'light';

            // Save to cookie via PHP endpoint
            fetch('/setTheme', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'theme=' + newTheme
            });
        });
    }
    </script>

</body>

</html>