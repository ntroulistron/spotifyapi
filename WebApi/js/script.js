document.addEventListener('DOMContentLoaded', function() {
    const genreSelect = document.getElementById('genre');
    const playlistSelect = document.getElementById('playlist');

    genreSelect.addEventListener('change', function() {
        fetchPlaylistsByGenre(genreSelect.value);
    });

    function fetchPlaylistsByGenre(genre) {
        fetch(`fetch_playlists.php?genre=${genre}`)
            .then(response => response.text())
            .then(data => {
                playlistSelect.innerHTML = data;
            })
            .catch(error => console.error('Error fetching playlists:', error));
    }
});


