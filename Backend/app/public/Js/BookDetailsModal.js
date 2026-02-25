function toggleAddressForm(id) {
        const useProfileAddress = document.getElementById('useProfileAddress').checked;
        const addressForm = document.getElementById('addressForm');
        if (useProfileAddress) {
            
            fetch(`/getProfileAddress/${id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('street').value = data.address || '';
                    document.getElementById('zip').value = data.post_code || '';
                    document.getElementById('state').value = data.state || '';
                    document.getElementById('city').value = data.state || '';
                    document.getElementById('country').value = data.country || '';
                })
                .catch(error => console.error('Error fetching profile address:', error));
        } else {
            // Clear the form fields
            document.getElementById('street').value = '';
            document.getElementById('zip').value = '';
            document.getElementById('state').value = '';
            document.getElementById('city').value = '';
            document.getElementById('country').value = '';
        }
    }


    function closeBookModal(){
        const modal = document.getElementById('popupOverlay');
        const modalContainer = document.getElementById('popupContainer');
        modalContainer.innerHTML = '';
        modal.style.display = 'none';
    }

    function openAddressForm(bookId) {
        const addressForm = document.getElementById('addressForm');
        addressForm.classList.remove('hidden');
        const bookOverview = document.getElementById('bookOverview');
        bookOverview.classList.add('hidden');
        const bookIdHidden = document.getElementById('bookIdHidden');
        bookIdHidden.value = bookId;
        
    }


function submitAddressForm() {
    const bookId = document.getElementById('bookIdHidden').value;
    const ownerId = document.getElementById('ownerIdHidden').value;
    const requesterId = document.getElementById('requesterIdHidden').value;
    const street = document.getElementById('street').value;
    const zip = document.getElementById('zip').value;
    const state = document.getElementById('state').value;
    const city = document.getElementById('city').value;
    const country = document.getElementById('country').value;
    const requestData = {
        bookId: bookId,
        ownerId: ownerId,
        requesterId: requesterId,
        street: street, 
        zip: zip,
        state: state,
        city: city,
        country: country
    };
    console.log('Submitting book request with data:', requestData);
    fetch('/createBookRequest', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(requestData)
    })
    .then(async response => {
        const text = await response.text();
        console.log('Raw response:', text);
        
        try {
            const data = JSON.parse(text);
            if (!response.ok) {
                throw new Error(data.message || 'Request failed');
            }
            return data;
        } catch (e) {
            // Response is not valid JSON
            console.error('Server response was not JSON:', text);
            throw new Error('Server error: ' + text.substring(0, 100));
        }
    })
    .then(data => {
        console.log(data.message);
        closeBookModal();
        // Redirect to checkout if redirectUrl is provided
        if (data.redirectUrl) {
            window.location.href = data.redirectUrl;
        }
    })
    .catch(error => {
        console.error('Error submitting book request:', error);
        alert('Error: ' + error.message);
    });
}

function openBookDetails(bookId, isFromListing) {
    const overlay = document.getElementById('popupOverlay');
    const modalContainer = document.getElementById('popupContainer');
    fetch(`/bookDetails/${bookId}`)
        .then(response => response.text())
        .then(html => {
            modalContainer.innerHTML = ''; // Clear previous content
            const modalDiv = document.createElement('div');
            modalDiv.innerHTML = html;
            modalContainer.appendChild(modalDiv);
            overlay.style.display = 'block';

            if (isFromListing) {
                openAddressForm(bookId);
            }

            if (overlay.style.display === 'block') {
                overlay.addEventListener('click', function(event) {
                    if (event.target === overlay) {
                        overlay.style.display = 'none';
                        modalContainer.innerHTML = '';
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error fetching book details:', error);
        });
}

function openRequesteeDetails(userId, requestId) {
    fetch(`/requesteeDetails/${userId}/${requestId}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('popupContainer').innerHTML = html;
            document.getElementById('popupOverlay').style.display = 'block';
        })
        .catch(error => {
            console.error('Error fetching book details:', error);
        });
}

// Close the modal when clicking outside of it
document.getElementById('popupOverlay').addEventListener('click', function(event) {
    if (event.target === this) {
        this.style.display = 'none';
    }
});