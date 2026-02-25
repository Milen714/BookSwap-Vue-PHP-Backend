function createBookPreview(bookData) {
    const previewContainer = document.getElementById('book-preview');
    previewContainer.innerHTML = `<div class="StepTwo flex flex-col mt-10 w-3/4 mx-auto p-6 rounded-md shadow-md bg-[#F2F0EF] dark:bg-[#0F0F0F] text-black dark:text-white">

        <h2 class="text-xl font-bold text-black dark:text-white" id="preview_title">Title</h2>

        <div class="w-full mt-4 flex flex-col ">
            <div class="mb-1 text-base font-medium text-black dark:text-white">Large</div>
            <div class="w-2/4 bg-[#ccc] dark:bg-[#2C3233] rounded-full h-2.5">
                <div class="bg-blue-600  h-2.5 rounded-full" style="width: 15%"></div>
            </div>
        </div>
        <div class="flex flex-col md:flex-row items-center gap-4 p-6 border border-[#ccc] dark:border-[#2C3233] rounded-md mt-6">
            <div id="preview_image" class="min-w-[116px] max-w-[116px] min-h-[160px] max-h-[160px] bg-center"
                style="background-image: url('http://books.google.com/books/content?id=PKV6swEACAAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api')">

            </div>
            <div class="">
                <div class="flex flex-row  mb-2">
                    <span
                        class="rounded-full bg-[#e5e5e5] dark:bg-[#151819] border border-[#ccc] dark:border-[#2C3233] px-2 py-1 text-sm font-semibold text-black dark:text-gray-200">ISBN: <span id="preview_isbn"></span></span>
                </div>
                <span id="preview_author" class="font-bold text-black dark:text-white">Author Name</span>

                <p id="preview_description" class="text-sm text-[#555] dark:text-[#7b8186] mb-2">
                    asd
                </p>
            </div>
        </div>
        <form method="POST" id="addBook-Form">
            
            <input type="hidden" id="hidden_isbn" name="isbn">
            

            <!-- User choices -->
            <div class="mt-4 mb-4 pb-4">
                <label for="condition" class="block mb-2.5 text-sm font-medium text-black dark:text-white">Book Condition:</label>
                <select name="condition" id="condition"
                    class="block w-1/4 px-3 py-2.5 bg-[#e5e5e5] dark:bg-[#151819] border border-[#ccc] dark:border-[#2C3233] text-black dark:text-white text-md rounded-md focus:ring-brand focus:border-brand shadow-xs placeholder:text-body"
                    required>
                    <option value="New">Like New</option>
                    <option value="Good">Gently Used</option>
                    <option value="Fair">Used</option>
                    <option value="Poor">Heavily Used</option>

                </select>
            </div>

            <div class="mt-4 mb-4 pb-4">
                <label for="userReview" class="block mb-2.5 text-sm font-medium text-black dark:text-white">Your Review:
                    (optional)</label>
                <p class="text-sm text-[#555] dark:text-[#7b8186] mb-2">Share why this book matters and you might spark an instant
                    swap.</p>
                <textarea name="userReview" id="userReview"
                    class="block w-full px-3 py-2.5 bg-[#e5e5e5] dark:bg-[#151819] border border-[#ccc] dark:border-[#2C3233] text-black dark:text-white text-md rounded-md focus:ring-brand focus:border-brand shadow-xs placeholder:text-body"></textarea>
            </div>

            <button class="button_primary" type="submit">Save Book</button>
        </form>
        

        <div id="test-div"></div>

    </div>`;

    // Populate preview with book data
    document.getElementById('preview_title').innerText = bookData.title || 'Title Not Available';
    document.getElementById('preview_author').innerText = bookData.author ? bookData.author : 'Author Not Available';
    document.getElementById('preview_description').innerText = bookData.description || 'Description Not Available';
    document.getElementById('preview_image').style.backgroundImage = `url('${bookData.thumbnail_image_url || 'default-image-url.jpg'}`;
    document.getElementById('preview_isbn').innerText = bookData.isbn || 'ISBN Not Available';

    // Set hidden input values
    document.getElementById('hidden_isbn').value = bookData.isbn || '';
}