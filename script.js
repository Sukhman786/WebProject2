document.addEventListener('DOMContentLoaded', () => {
    
    // --- FEATURE 1: BOOKING FORM ---
    const submitBtn = document.getElementById('submitBtn');
    const bookingForm = document.getElementById('bookingForm');
    
    if (submitBtn && bookingForm) {
        submitBtn.addEventListener('click', function() {
            if (!bookingForm.checkValidity()) {
                bookingForm.reportValidity(); 
                return;
            }

            const formData = new FormData(bookingForm);

            fetch('booking.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                const trackID = document.getElementById('trackID');
                if(trackID) trackID.innerText = data;
            
                const responseArea = document.getElementById('response-text');
                if(responseArea) responseArea.style.display = 'flex';
                
                // Hide booking related elements
                document.querySelectorAll('.input-form, .form-section-title, .h1-contact, .input-btn:not(.nav-btn)').forEach(el => {
                    el.style.display = 'none';
                });
            })
            .catch(error => console.error('Booking Error:', error));
        });
    }

    // --- FEATURE 2: CONTACT FORM (The one you are fixing) ---
    const contactBtn = document.getElementById('contact-btnji');
    const nameInput = document.getElementById('contact-nameji');
    const responseText = document.getElementById('response-text');

    if (contactBtn) {
        contactBtn.addEventListener('click', function(e) {
            e.preventDefault(); 

            const userName = nameInput.value.trim();

            if (userName === "") {
                alert("Please enter your name first!");
                return;
            }

            // Update content
            responseText.innerHTML = `Thank You! <span>${userName}</span>, We will <span>contact you</span> soon!`;

            // Reveal the message
            responseText.style.display = 'block';

            // Hide the input fields and button so only the message shows (Optional)
            // If you want the form to disappear like the booking one does:
            // nameInput.style.display = 'none';
            // contactBtn.style.display = 'none';
            // document.querySelector('.form-contact textarea').style.display = 'none';
            
            console.log("Contact message triggered for: " + userName);
        });
    }
});

// FEATURE 3: CALCULATOR (Needs to be called via oninput in HTML)
function calculateTotal() {
    const weightInput = document.getElementById('weight');
    const parcelTypeInput = document.getElementById('parcelType');
    const deliveryTypeInput = document.getElementById('deliveryType');
    const display = document.getElementById('totalDisplay');

    if (!weightInput || !parcelTypeInput || !deliveryTypeInput || !display) return;

    const weight = parseFloat(weightInput.value) || 0;
    const multiplier = parseFloat(deliveryTypeInput.value) || 1;
    let basePrice = weight * 0.5;

    if (parcelTypeInput.value === "fragile") basePrice += 50;
    if (parcelTypeInput.value === "electronics") basePrice += 100;

    display.innerText = (basePrice * multiplier).toFixed(2);
}