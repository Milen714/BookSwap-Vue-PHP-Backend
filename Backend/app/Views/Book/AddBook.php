<?php

namespace App\Views\Book;
// require_once 'config.php';
?>
<h1 class="text-center m-5 text-3xl font-semibold text-colors">List Your Book</h1>
<div class="BookListingContainer">
    <article
        class="StepOne max-w-md mx-auto bg-colors border border-[#ccc] dark:border-[#2C3233] text-colors p-6 rounded-md shadow-md">
        <!-- <form method='POST' action='/addBook'> -->
        <form id="ISBN-form" method='POST'>
            <article class="input_group">
                <label class="input_label text-colors" for="ISBN">ISBN:</label>
                <input class="form_input bg-[#e5e5e5] dark:bg-[#2C3233] text-colors" type="text" id="ISBN" name="isbn"
                    required>
            </article>
            <?php if (isset($error)): ?>
            <div class="mb-4 p-4 bg-red-300 text-red-900 border border-red-400 rounded">
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>
            <button id="addBookButton" class="button_primary" type="submit">Add Book</button>
        </form>
        <div class="my-4 border-t border-[#ccc] dark:border-[#2C3233] text-center text-colors font-semibold">OR</div>
        <div class="mt-4 flex flex-col items-center">
            <div class="w-full">
                <label for="imageInput" class="block mb-2.5 text-sm font-medium text-colors">Scan Your
                    Book</label>
                <label for="imageInput"
                    class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-[#ccc] dark:border-[#2C3233] rounded-lg cursor-pointer bg-[#e5e5e5] dark:bg-[#1a1a1a] hover:bg-[#d5d5d5] dark:hover:bg-[#222222] transition-colors">
                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                        <svg class="w-8 h-8 mb-3 text-[#555] dark:text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <p class="text-sm text-[#555] dark:text-gray-400"><span
                                class="font-semibold text-blue-500">Click to scan</span>
                            or take a photo</p>
                    </div>
                    <input type="file" name="picture" capture="environment" id="imageInput" accept="image/*"
                        class="hidden">
                </label>
            </div>
            <img class="mt-4" id="preview" style="max-width:200px; display:none;">
        </div>
    </article>
    <div id="spinner" class="hidden">
        <div class="loader"></div>
    </div>

    <div id="book-preview" class="mt-10"></div>
</div>

<script src="https://unpkg.com/@zxing/library@latest"></script>
<script src="/Js/bookPreview.js"></script>
<script src="/Js/createBookPreview.js"></script>
<script src="/Js/barcodeScanner.js"></script>




<script>
function displayConfirmationMessage(message) {
    const testDiv = document.getElementById('test-div');
    fetch('/bookPostConfirmation')
        .then(response => response.text())
        .then(html => {
            testDiv.innerHTML = html;
        })
        .catch(error => console.error('Error fetching confirmation message:', error));
}
</script>