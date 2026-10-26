document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll('.join_button').forEach(function(button) {
        button.addEventListener('click', function() {
            var meetingId = this.dataset.id;

            fetch('/meeting', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id: meetingId
                })
            })
                .then(function(response) {
                    if (!response.ok) {
                        throw new Error("HTTP error " + response.status);
                    }
                    return response.text();
                })
                .then(function() {
                    button.textContent = 'Joined';
                    button.disabled = true;
                })
                .catch(function(error) {
                    console.error("Error occurred:", error);
                });
        });
    });
});