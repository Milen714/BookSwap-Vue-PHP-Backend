function initFlipCards() {
    document.querySelectorAll(".flip-card").forEach(card => {
        // Skip if already initialized
        if (card.dataset.flipInitialized) return;
        card.dataset.flipInitialized = 'true';
        
        const front = card.querySelector(".card-flip-front");
        const back = card.querySelector(".card-flip-back");

        card.addEventListener("click", () => {
            front.classList.toggle("hidden");
            back.classList.toggle("hidden");
            card.classList.toggle("flip");
            back.classList.toggle("flip");
        });
    });
}

// Initialize on page load
initFlipCards();
