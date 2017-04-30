<!DOCTYPE html>
<html>
<head>
	<META http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link href="style.css" rel="stylesheet" media="all" type="text/css" />
	<TITLE>Spotify API fetch</TITLE>
</head>
<body>
	<header>
		Spotify api search query
	</header>
	<div class="search_results">
<?php
	session_start();

	// Spotify api urls
	$url_base = "https://api.spotify.com/v1/search?";
	$endpoint_search_type = "&type=";

	// Initialize curl session
	$handle = curl_init();
	$error = false;
	$url_found = false;
	
	if ( isset($_POST["previous"]) ) {
		$url = $_POST["previous"];
		$url_found = true;
	}
	if ( isset($_POST["next"]) ) {
		$url = $_POST["next"];
		$url_found = true;
	}
	if ( isset($_POST["selection"]) ) {
		$_SESSION["selection"] = $_POST["selection"];
	}
	
	// Skip search variable if url already set_error_handler
	if ( !$url_found ) {
		// Check search variable
		if ( !isset($_POST["search"]) ) {
			echo "Error! No search variable!<br>";
			$error = true;
		} else if ( empty( $_POST["search"] ) ) {
			echo "Error! No search variable!<br>";
			$error = true;
		}
		
		// Modify search string, change quotation marks " to %22
		// and spaces to +
		$searchstr = str_replace( " ", "+", $_POST["search"] );
		$searchstr = str_replace( "\"", "%22", $searchstr );
	
		// Construct search url
		$url = $url_base . "q=" . $searchstr . $endpoint_search_type . $_POST["selection"];
	}
	
	if ( !$error ) {
		
		// Set curl options (default method is GET, so no option fr that is needed)
		curl_setopt( $handle, CURLOPT_URL, $url );
		curl_setopt( $handle, CURLOPT_SSL_VERIFYPEER, false ); // Turn of ssl peer verification
		curl_setopt( $handle, CURLOPT_RETURNTRANSFER, true ); // Set transfer to string
		
		// Execute session
		$json_result = curl_exec( $handle );
		
		// Check for errors
		if ( $json_result == false ) {
			echo curl_errno( $handle ) . ": " . curl_error( $handle );
		} else {
			$result_array = json_decode( $json_result, true );
			// debug
			// print_r( $result_array ); echo "<br>";
			
			// Handle results
			
			// Present results in table
			print "<table>";
			if ( $_SESSION["selection"] == "artist" ) {
				$tmp = "artists";
				print "<tr>";
				print "<th>Artist name</th>";
				print "</tr>";
				foreach( $result_array["artists"]["items"] as $item) {
					print "<tr>";
					print "<td>" . $item["name"] . "</td>";
					print "</tr>";
				}
			} else if ( $_SESSION["selection"] == "album" ) {
				$tmp = "albums";
				print "<tr>";
				print "<th>Artists</th>";
				print "<th>Album</th>";
				print "</tr>";
				foreach( $result_array["albums"]["items"] as $item) {
					print "<tr>";
					print "<td>";
					print "<table>";
					foreach( $item["artists"] as $i ) {						
						print "<tr><td id=\"no_border\">" . $i["name"] . "</td></tr>";
					}
					print "</table>";
					print "</td>";
					print "<td>" . $item["name"] . "</td>";
					print "</tr>";
				}
			} else if ( $_SESSION["selection"] == "track" ) {
				$tmp = "tracks";
				print "<tr>";
				print "<th>Artists</th>";
				print "<th>Album</th>";
				print "<th>Track</th>";
				print "</tr>";
				foreach( $result_array["tracks"]["items"] as $item) {
					print "<tr>";
					print "<td>";
					print "<table>";
					foreach( $item["artists"] as $i ) {						
						print "<tr><td id=\"no_border\">" . $i["name"] . "</td></tr>";
					}
					print "</table>";
					print "</td>";
					print "<td>" . $item["album"]["name"] . "</td>";
					print "<td>" . $item["name"] . "</td>";
					print "</tr>";
				}
			} else if ( $_SESSION["selection"] == "playlist" ) {
				$tmp = "playlists";
				print "<tr>";
				print "<th>Playlist name</th>";
				print "<th>Tracks</th>";
				print "</tr>";
				foreach( $result_array["playlists"]["items"] as $item) {
					print "<tr>";
					print "<td>" . $item["name"] . "</td>";
					print "<td>" . $item["tracks"]["total"] . "</td>";
					print "</tr>";
				}
			}
			print "</table><br>";
			print "</div>"; // end search results box
			
			// Show information about shown entries
			print "<div class=\"bottom\">";
			
			// debug
			// echo "Search query: " . $url . "<br>";
			
			print "Total results: " . $result_array[$tmp]["total"] . "<br>";
			print "Showing results: ";
			if ( $result_array[$tmp]["total"] <= $result_array[$tmp]["limit"] ) {
				print "0 - " . $result_array[$tmp]["total"] . "<br>";
			} else {
				if ( $result_array[$tmp]["offset"] == NULL ) {
					print "0 - " . $result_array[$tmp]["limit"] . "<br>";
				} else {
					print $result_array[$tmp]["offset"];
					print " - ";
					if ( ($result_array[$tmp]["offset"] + $result_array[$tmp]["limit"] > $result_array[$tmp]["total"]) ) {
						print $result_array[$tmp]["total"];
					} else {
						print $result_array[$tmp]["offset"] + $result_array[$tmp]["limit"];
					}
					print "<br>";
				}
			}
			
			// Next and previous buttons
			if ( $result_array[$tmp]["total"] > $result_array[$tmp]["limit"] ) {
				print "<form action=\"spotify_fetch.php\" method=\"post\">";
				// Store urls to previous and next entries
				if ( $result_array[$tmp]["previous"] != NULL ) {
					print "<button name=\"previous\" value=\"" . $result_array[$tmp]["previous"] . "\">Previous";
				} else {
					print "<button name=\"previous\" disabled>Previous";
				}
				print "</button>";
				if ( $result_array[$tmp]["next"] != NULL ) {
					print "<button name=\"next\" value=\"" . $result_array[$tmp]["next"]. "\">Next";
				} else {
					print "<button name=\"next\" disabled>Next";
				}
				print "</button>";
				
				print "</form>";
			}
		} // if ( $json_result != false )
	
	} else { // if ( !$error )
		print "</div>"; // end search results box
		print "<div class=\"bottom\"";
	}
	
	// Close handle
	curl_close( $handle );
	
	// Empty $_POST
	unset($_POST["previous"]);
	unset($_POST["next"]);
	
	echo "<a href=\"index.php\">Back</a><br>";
	print "</div>"; // Bottom box
?>
	<footer>
		<div id="footer-content">
			Written by Kari Siivonen, Student, Tampere University of Technology
		</div>
	</footer>

</body>
</html> 