function createSpinner() {
    const booksSection = document.getElementById('booksSection');
    booksSection.innerHTML = `<div id="spinner" class="w-full flex justify-center items-center py-10">
        <div class="loader ease-linear rounded-full border-8 border-t-8 border-gray-200 h-16 w-16"></div>
    </div>`;
}

async function fetchBooksFromSearch(event) {
    if (event) event.preventDefault();
    const booksSection = document.getElementById('booksSection');
    const genreSelect = document.getElementById('genre-select');
    const searchInput = document.getElementById('search-input');
    
    const selectedGenre = genreSelect.value;
    const generalSearch = searchInput.value;
    console.log('Searching books with genre:', selectedGenre, 'and search term:', generalSearch);

    createSpinner();

    try {
        await fetch(`/searchBooks?genre=${encodeURIComponent(selectedGenre)}&search=${encodeURIComponent(generalSearch)}`)
        .then(async response => response.text())
        .then(html => {
        booksSection.innerHTML = "";
        booksSection.innerHTML = html;
        // Update URL without reloading the page 
        // to reflect current search parameters and will be use in future pagination implementation 
        // to retain filters through the pages
        const newURL = `${window.location.pathname}?genre=${encodeURIComponent(selectedGenre)}&search=${encodeURIComponent(generalSearch)}`;
        window.history.replaceState({}, '', newURL);
        
        // Re-initialize flipcards for newly loaded content.
        // This due to the dinamic loading of book posts. The DOM elements are new(Books from search)
        // and need to have the flipcard functionality re-applied

        if (typeof initFlipCards === 'function') {
            initFlipCards();
        }
    })
    } catch (error) {
        console.error('Error fetching books:', error);
        booksSection.innerHTML = `<p class="text-red-500">Error fetching books. Please try again later.</p>`;
    } finally {
        const spinner = document.getElementById('spinner');
        if (spinner) {
            spinner.remove();
        }
    }
};