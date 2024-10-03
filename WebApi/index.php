<?php
// Include the required files
require_once 'spotify_token.php';
require_once 'spotify_search.php';

// Get the access token from the included file
$access_token = $access_token;

// Fetch genres
$genres = getSpotifyGenres($access_token);
$selected_genre = isset($_GET['genre']) ? $_GET['genre'] : '';
$selected_playlist = isset($_GET['playlist']) ? $_GET['playlist'] : '';


// Debugging output
// echo "Selected Genre: " . $selected_genre . "<br>";
// echo "Selected Playlist: " . $selected_playlist . "<br>";


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spotify Web Api</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="nav-wrapper">
            <a href="#" class="brand-logo">
                <img src="images/spotify.png" alt="Spotify Web Api" class="logo">
                Spotify Web Api
            </a>
            <ul id="nav-mobile" class="right hide-on-med-and-down">
                <li><a id='home'href="#">Home</a></li>
                <li><a href="#">Search</a></li>
                <li><a href="#">Your Library</a></li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Sidebar -->
        <div class="sidebar">
            <form id="searchForm" method="GET" action="index.php">
                <div class="input-field">
                    <label for="genre">Genre:</label>
                    <select name="genre" id="genre" class="browser-default">
                        <?php
                        if (!empty($genres->genres)) {
                            foreach ($genres->genres as $genre) {
                                echo '<option value="' . $genre . '"' . ($selected_genre == $genre ? ' selected' : '') . '>' . ucfirst($genre) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="input-field">
                    <label for="playlist">Playlists:</label>
                    <select name="playlist" id="playlist" class="browser-default">
                        <!-- Playlist options will be populated dynamically based on the selected genre -->
                    </select>
                </div>
                <div class="input-field">
                    <input type="text" name="query" id="query" placeholder="Search for a song" value="<?php echo isset($_GET['query']) ? htmlspecialchars($_GET['query']) : ''; ?>">
                </div>
                <button type="submit" name="submit" class="btn">Search</button>
            </form>
        </div>

        <!-- Search Results -->
        <div class="search-results">
            <?php
            if (!empty($_GET['query'])) {
                $query = $_GET['query']; // Retrieve the search query from the form submission
                $selected_genre = $_GET['genre'] ?? '';
                $selected_playlist = $_GET['playlist'] ?? '';

                // Search for songs, including the selected genre and playlist parameters
                $search_results = searchSpotifySongs($query, $access_token, $selected_genre, $selected_playlist);

                // Display the results
                if (!empty($search_results->tracks->items)) {
                    echo '<div class="results-container">';
                    
                    foreach ($search_results->tracks->items as $track) {
                        echo '<div class="track-card">';
                        if (!empty($track->album->images)) {
                            echo '<img src="' . $track->album->images[0]->url . '" alt="Album Art" class="track-image">';
                        } else {
                            echo '<img src="placeholder-image-url.jpg" alt="No Album Art" class="track-image">';
                        }
                        echo '<div class="track-info">';
                        echo '<p class="track-title">' . $track->name . '</p>';
                        echo '<p class="track-artist">' . $track->artists[0]->name . '</p>';
                        echo '<p class="track-album">' . $track->album->name . '</p>';
                        if ($track->preview_url) {
                            echo '<a href="' . $track->preview_url . '" class="track-preview">Listen</a>';
                        } else {
                            echo '<span class="no-preview">No preview available</span>';
                        }
                        echo '</div>';
                        echo '</div>';
                    }

                    echo '</div>';
                } else {
                    echo '<p>No results found.</p>';
                }
            }
            ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="page-footer">
        <div class="container">
            © 2024 Spotify Web Api
        </div>
    </footer>

    <script src="js/script.js"></script>
    <script>
    // Function to perform the search asynchronously
    function search(query) {
        // Get the selected genre and playlist
        var genre = document.getElementById('genre').value;
        var playlist = document.getElementById('playlist').value;

        // Perform the search using AJAX
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'index.php?genre=' + encodeURIComponent(genre) + '&playlist=' + encodeURIComponent(playlist) + '&query=' + encodeURIComponent(query), true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                // Update the search results container with the response
                document.getElementById('searchResults').innerHTML = xhr.responseText;
            }
        };
        xhr.send();
    }

    // Fetch playlists based on the selected genre
    function fetchPlaylists() {
        var genre = document.getElementById('genre').value;
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'fetch_playlists.php?genre=' + encodeURIComponent(genre), true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                // Update the playlists dropdown with the fetched playlists
                document.getElementById('playlist').innerHTML = xhr.responseText;
            }
        };
        xhr.send();
    }

    // Add event listener to genre dropdown
    document.getElementById('genre').addEventListener('change', function() {
        // Fetch playlists when genre selection changes
        fetchPlaylists();
    });

    // Fetch playlists initially if a genre is selected
    if (document.getElementById('genre').value !== '') {
        fetchPlaylists();
    }
</script>
</body>
</html>



