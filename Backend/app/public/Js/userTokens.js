addEventListener('DOMContentLoaded', function() {
    fetch('/getUserTokens', {
        method: 'GET',
        headers: { 'Content-Type': 'application/json' }
    })
    .then(async response => {   
        const data = await response.json();
        if (!response.ok) {
            throw new Error(data.message || 'Failed to fetch user tokens');
        }
        document.querySelector('#userTokens').textContent = data.tokens;
    })
    .catch(error => {
        console.error('Error fetching user tokens:', error);
    });
});