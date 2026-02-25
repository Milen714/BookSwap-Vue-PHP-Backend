document.querySelectorAll('button[data-next-status]').forEach(button => {
    button.addEventListener('click', function() {
        const requestId = this.dataset.requestId;
        const nextStatus = this.dataset.nextStatus;
        console.log(`Request ID: ${requestId}, Next Status: ${nextStatus}`);
        
        fetch(`/updateRequest?requestId=${requestId}&status=${nextStatus}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ status: nextStatus, requestId: requestId })
        })
        .then(async response => {
            const data = await response.json();
            if (!response.ok) {
                throw new Error(data.message || 'Request failed');
            }
            return data;
        })
        .then(data => {
            location.reload();
        })
        .catch(error => {
            console.error('Error updating request status:', error);
        });
    });
});