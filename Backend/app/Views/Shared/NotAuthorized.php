<?php
$theme = $_COOKIE['theme'] ?? 'light';
$darkClass = $theme === 'dark' ? 'dark' : '';
?>
<!DOCTYPE html>
<html lang="en" class="<?php echo $darkClass; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Access Denied | BookSwap</title>
    <link rel="icon" type="image/svg+xml" href="/Assets/Favicon.svg">
    <link rel="stylesheet" href="/css/style.css">
</head>

<body class="bg-[#F2F0EF] dark:bg-[#0F0F0F] min-h-screen flex items-center justify-center p-4">
    <div class="text-center max-w-lg">
        <!-- Lock Animation -->
        <div class="relative mb-8">
            <div class="flex justify-center">
                <!-- Animated lock -->
                <div class="relative">
                    <svg class="w-32 h-32 text-red-500 animate-pulse" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <!-- Shaking keyhole -->
                    <div
                        class="absolute top-1/2 left-1/2 transform -translate-x-1/2 translate-y-1 text-2xl animate-bounce">
                        🔐
                    </div>
                </div>
            </div>
            <!-- Floating "no entry" signs -->
            <div class="absolute top-0 left-1/4 text-2xl animate-bounce" style="animation-delay: 0.2s;">🚫</div>
            <div class="absolute top-4 right-1/4 text-2xl animate-bounce" style="animation-delay: 0.5s;">⛔</div>
        </div>

        <!-- 403 Text -->
        <h1 class="text-8xl font-bold text-black dark:text-white mb-2">
            4<span class="inline-block text-red-500">🔒</span>3
        </h1>

        <h2 class="text-2xl font-semibold text-black dark:text-white mb-4">
            Whoa there, bookworm! This section is off-limits.
        </h2>

        <p class="text-gray-600 dark:text-gray-400 mb-8 text-lg">
            Looks like you don't have the right library card for this area.
            Some books are just not meant for everyone!
        </p>

        <!-- Fun messages that rotate -->
        <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4 mb-8 border border-red-200 dark:border-red-800/50">
            <p class="text-sm text-red-700 dark:text-red-300 italic" id="funMessage">
                "This book is in the restricted section..."
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="/"
                class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all transform hover:scale-105 flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Back to Browse
            </a>
            <a href="/login"
                class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-all transform hover:scale-105 flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                </svg>
                Login
            </a>
            <button onclick="history.back()"
                class="px-6 py-3 bg-transparent border-2 border-[#2C3233] dark:border-gray-600 text-black dark:text-white font-medium rounded-lg hover:bg-[#CBCBCB] dark:hover:bg-[#222222] transition-all transform hover:scale-105 flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Go Back
            </button>
        </div>

        <!-- Footer -->
        <p class="mt-12 text-gray-500 dark:text-gray-500 text-sm">
            Think this is a mistake? Try <a href="/login" class="text-blue-500 hover:underline">logging in</a> with a
            different account.
        </p>
    </div>

    <script>
    // Rotate fun messages
    const messages = [
        "\"This book is in the restricted section...\"",
        "\"Sorry, your library card doesn't have VIP access\"",
        "\"You shall not pass! – The Librarian\"",
        "\"Access denied: Insufficient reading level\"",
        "\"This page requires a special bookmark\"",
        "\"Error 403: You need a higher reader rank\"",
        "\"The librarian is watching... and says no\"",
        "\"Some stories aren't meant for everyone\"",
    ];

    let currentIndex = 0;
    const messageEl = document.getElementById('funMessage');

    setInterval(() => {
        currentIndex = (currentIndex + 1) % messages.length;
        messageEl.style.opacity = 0;
        setTimeout(() => {
            messageEl.textContent = messages[currentIndex];
            messageEl.style.opacity = 1;
        }, 300);
    }, 4000);

    messageEl.style.transition = 'opacity 0.3s ease';
    </script>
</body>

</html>