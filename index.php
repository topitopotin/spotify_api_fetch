<!DOCTYPE html>
<html>
<head>
	<META http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link href="style.css" rel="stylesheet" media="all" type="text/css" />
	<TITLE>Spotify API search query</TITLE>
</head>
<body>
	<?php session_start(); ?>
	<header>
		Spotify api search query
	</header>
	<div class="wrapper">
		<div class="left flex">
			Make searches to the spotify database by using the provided form.<br>
			You can use the standard search methods (wildcards *, double quotes etc.).<br>
			An indepth description of the supported methods can be found at:<br>
			<a href="https://developer.spotify.com/web-api/search-item/">Spotify web api documentation</a><br><br>
			You can search for artists, albums, tracks or playlists with the given search parameter.
		</div>
		<div class="right flex">
			<form action="spotify_fetch.php" method="post">
				Search for: 
				<select name="selection">
					<option value="artist">Artist</option>
					<option value="track">Track</option>
					<option value="album">Album</option>
					<option value="playlist">Playlist</option>
				</select><br>
				Search query: <input type="text" name="search"><br>
				<input type="submit" name="submit"><input type="reset" name="reset">
			</form>
		</div>
	</div>
	<footer>
		<div id="footer-content">
			Written by Kari Siivonen, Student, Tampere University of Technology
		</div>
	</footer>
</body>
</html>