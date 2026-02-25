const previewContainer = document.getElementById('book-preview');
const ISBNForm = document.getElementById('ISBN-form');
const spinner = document.getElementById('spinner');
const addBookButton = document.getElementById('addBookButton');

ISBNForm.addEventListener('submit', async function(event) {
    event.preventDefault();

    const data = {
        isbn: document.getElementById('ISBN').value
    };

    previewContainer.innerHTML = "";
    spinner.classList.remove('hidden');
    addBookButton.disabled = true;

    //show loading spinner
    try{ 
        const response = await fetch('/fetchBookPreview', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });

        if (!response.ok) {
            throw new Error("Server error: " + response.statusText);
        }
        const bookData = await response.json();
        console.log('Book data received:', bookData);
        createBookPreview(bookData);
    } catch (error) {
        console.error('Error fetching book data:', error);
        previewContainer.innerHTML = `<p class="text-red-500">Error fetching book data. Please try again later.</p>`;
    } finally {
        //hide loading spinner if implemented
        spinner.classList.add('hidden');
        addBookButton.disabled = false;
    }
        
});