document.getElementById('submitBtn').addEventListener('click', function() {
    const form = document.getElementById('bookingForm');

    if (!form.checkValidity()) {
        form.reportValidity(); 
        return;
    }

    const formData = new FormData(form);

    fetch('booking.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        // 'data' now contains the ID (e.g., S001) sent back by Oracle/PHP
        document.getElementById('trackID').innerText = data;
    
        const responseArea = document.getElementById('response-text');
        responseArea.style.display = 'flex';
        
        document.querySelectorAll('.input-form, .form-section-title, .h1-contact, .input-btn:not(.nav-btn)').forEach(el => {
            el.style.display = 'none';
        });
    })
    .catch(error => console.error('Error:', error));
});


function calculateTotal() {
    const weightInput = document.getElementById('weight');
    const parcelTypeInput = document.getElementById('parcelType');
    const deliveryTypeInput = document.getElementById('deliveryType');
    const display = document.getElementById('totalDisplay');

    if (!weightInput || !parcelTypeInput || !deliveryTypeInput || !display) {
        console.error("One or more HTML elements were not found. Check your IDs.");
        return;
    }

    const weight = parseFloat(weightInput.value) || 0;
    const parcelType = parcelTypeInput.value;
    const multiplier = parseFloat(deliveryTypeInput.value);
    
    let basePrice = weight * 0.5;

    if (parcelType === "fragile") basePrice += 50;
    if (parcelType === "electronics") basePrice += 100;

    const finalTotal = basePrice * multiplier;

    display.innerText = finalTotal.toFixed(2);
}