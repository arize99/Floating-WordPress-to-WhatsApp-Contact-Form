// Toggle popup visibility
document.addEventListener('DOMContentLoaded', function () {
    var whatsappButton = document.querySelector('.wcf-floating-button');
    var popup = document.querySelector('.wcf-popup');
    var closeBtn = document.querySelector('.wcf-close-btn');

    whatsappButton.addEventListener('click', function () {
        popup.style.display = 'block';
    });

    closeBtn.addEventListener('click', function () {
        popup.style.display = 'none';
    });

    window.addEventListener('click', function (e) {
        if (e.target == popup) {
            popup.style.display = 'none';
        }
    });
});

// Send data to WhatsApp
function sendToWhatsapp() {
    // Grab form data
    var name = document.getElementById('wcf-name').value;
    var email = document.getElementById('wcf-email').value;
    var tel = document.getElementById('wcf-tel').value;
    var message = document.getElementById('wcf-message').value;

    // Validate input fields
    if (!name || !email || !tel || !message) {
        alert('Please fill in all fields');
        return;
    }

    // Prepare WhatsApp message
    var whatsappMessage = 'Name: ' + name + '\nEmail: ' + email + '\nTel: ' + tel + '\nMessage: ' + message;

    // Use phone number from settings
    var whatsappUrl = 'https://wa.me/' + wcf_data.phone_number + '?text=' + encodeURIComponent(whatsappMessage);
    window.open(whatsappUrl, '_blank');
}
