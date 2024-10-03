document.addEventListener('DOMContentLoaded', function() {
    const homeButton = document.getElementById('home'); // Adjust ID as necessary

    homeButton.addEventListener('click', function() {
        window.location.reload();
    });
});
