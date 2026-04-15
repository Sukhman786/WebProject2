// FEATURE 3: CALCULATOR (Moved outside so HTML can access it globally)
function calculateTotal() {
    const weightInput = document.getElementById('weight');
    const parcelTypeInput = document.getElementById('parcelType');
    const deliveryTypeInput = document.getElementById('deliveryType');
    const display = document.getElementById('totalDisplay');

    // Basic safety check
    if (!weightInput || !parcelTypeInput || !deliveryTypeInput || !display) return;

    const weight = parseFloat(weightInput.value) || 0;
    const multiplier = parseFloat(deliveryTypeInput.value) || 1;
    let basePrice = weight * 0.5;

    if (parcelTypeInput.value === "fragile") basePrice += 50;
    if (parcelTypeInput.value === "electronics") basePrice += 100;

    display.innerText = (basePrice * multiplier).toFixed(2);
}




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
                
                document.querySelectorAll('.input-form, .form-section-title, .h1-contact, .input-btn:not(.nav-btn)').forEach(el => {
                    el.style.display = 'none';
                });
            })
            .catch(error => console.error('Booking Error:', error));
        });
    }

    // --- FEATURE 2: CONTACT FORM ---
    const contactForm = document.querySelector('.form-contact');
    if (contactForm) {
        contactForm.addEventListener('submit', function(event) {
            event.preventDefault();

            // Use 'this' to find the specific input inside the form
            const nameInput = this.querySelector('input[type="text"]');
            const nameji = nameInput ? nameInput.value.trim() : "Friend";
            
            const btn = this.querySelector('.input-btn');
            const responseText = document.querySelector('#response-text');

            if (btn) btn.style.display = "none";

            if (responseText) {
                responseText.innerHTML = `Thank you, <span class="hidMsgSpan">${nameji}</span>! We will <span class="hidMsgSpan">contact you</span> soon.`;
                responseText.style.display = "block";
            }

            this.reset();
        });
    }
});