<?php
$theme = $_COOKIE['theme'] ?? 'light';
$darkClass = $theme === 'dark' ? 'dark' : '';
?>
<!DOCTYPE html>
<html lang="en" class="<?php echo $darkClass; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | BookSwap</title>
    <link rel="icon" type="image/svg+xml" href="/Assets/Favicon.svg">
    <link rel="stylesheet" href="/css/style.css">
</head>

<body class="bg-[#F2F0EF] dark:bg-[#0F0F0F] min-h-screen flex items-center justify-center p-4">
    <div class="text-center max-w-lg">
        <!-- Animated Book Stack -->
        <div class="relative mb-8">
            <div class="flex justify-center items-end gap-1">
                <!-- Falling books animation -->
                <div class="w-8 h-24 bg-blue-500 rounded-sm transform rotate-[-15deg] animate-bounce"
                    style="animation-delay: 0s; animation-duration: 2s;"></div>
                <div class="w-10 h-32 bg-emerald-500 rounded-sm transform rotate-[5deg] animate-bounce"
                    style="animation-delay: 0.2s; animation-duration: 2.2s;"></div>
                <div class="w-8 h-28 bg-amber-500 rounded-sm transform rotate-[-8deg] animate-bounce"
                    style="animation-delay: 0.4s; animation-duration: 1.8s;"></div>
                <div class="w-9 h-20 bg-rose-500 rounded-sm transform rotate-[12deg] animate-bounce"
                    style="animation-delay: 0.1s; animation-duration: 2.1s;"></div>
                <div class="w-7 h-26 bg-purple-500 rounded-sm transform rotate-[-3deg] animate-bounce"
                    style="animation-delay: 0.3s; animation-duration: 1.9s;"></div>
            </div>
            <!-- Sad face on top -->
            <div class="absolute -top-4 left-1/2 transform -translate-x-1/2 text-6xl animate-pulse">
                📚
            </div>
        </div>

        <!-- 404 Text -->
        <h1 class="text-8xl font-bold text-black dark:text-white mb-2">
            4<span class="inline-block animate-spin" style="animation-duration: 3s;">📖</span>4
        </h1>

        <h2 class="text-2xl font-semibold text-black dark:text-white mb-4">
            Oops! This page got lost in the library...
        </h2>

        <p class="text-gray-600 dark:text-gray-400 mb-8 text-lg">
            Looks like the book you're looking for has been checked out,
            misplaced, or never existed in our collection.
        </p>

        <!-- Fun messages that rotate -->
        <div class="bg-white/50 dark:bg-white/10 rounded-lg p-4 mb-8 border border-[#ccc] dark:border-[#2C3233]">
            <p class="text-sm text-gray-700 dark:text-gray-300 italic" id="funMessage">
                "A book lover's worst nightmare: Error 404"
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
            Still lost? Try searching for a book on our <a href="/" class="text-blue-500 hover:underline">homepage</a>
        </p>
    </div>

    <script>
    // Rotate fun messages
    const messages = [
        "\"A book lover's worst nightmare: Error 404\"",
        "\"This page is more lost than a bookmark in a library\"",
        "\"Even our best librarian couldn't find this page\"",
        "\"Plot twist: The page doesn't exist!\"",
        "\"This URL has been returned to the wrong shelf\"",
        "\"404: The page has been overdue since forever\"",
        "\"Someone forgot to renew this page's lease\"",
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