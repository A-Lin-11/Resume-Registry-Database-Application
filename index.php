<?php
require_once "pdo.php"; 
session_start();
?>

<!DOCTYPE html>
<html>
<head>
<title>Andrew Jun Lin - Resume Registry</title>
<?php require_once "bootstrap.php"; ?>
</head>
<body>
<div class="container">
<h1>Andrew Jun Lin's Resume Registry</h1>
<?php
if (isset($_SESSION['name'])) {
	if ( isset($_SESSION['message'])) {
	    echo '<p style="color:green">'.$_SESSION['message']."</p>\n";
	    unset($_SESSION['status']);
	    unset($_SESSION['message']);	    
	}
	if ( isset($_SESSION['error'])) {
	    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
	    unset($_SESSION['error']);	    
	}
	if ( isset($_SESSION['success'])) {
	    echo '<p style="color:green">'.$_SESSION['success']."</p>\n";
	    unset($_SESSION['success']);	    
	}

	echo '<p><a href="logout.php">Logout</a></p>';
	$stmt = $pdo->query("SELECT first_name, last_name, headline, profile_id FROM Profile");
	if ($stmt->rowCount() > 0) {
		echo '<table border="1">'."\n".
		"<tr>
			<th>Name</th>
		    <th>Headline</th>
		    <th>Action</th></tr>";
			$stmt = $pdo->query("SELECT first_name, last_name, headline, profile_id FROM Profile");
			while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
				echo("<tr><td>".htmlentities($row['first_name'])." ".htmlentities($row['last_name'])."</td>");
				echo("<td>".htmlentities($row['headline'])."</td>");
				echo("<td>".'<a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a> / ');
				echo('<a href="delete.php?profile_id='. $row['profile_id'].'">Delete</a>'."</td></tr>");	
			}
		echo '</table>';
	} 

	echo '<p><a href="add.php">Add New Entry</a></p>';
	// unset($_SESSION['name']);
} else {
	echo '<p><a href="login.php">Please log in</a></p>';


	$stmt = $pdo->query("SELECT first_name, last_name, headline, profile_id FROM Profile");
		if ($stmt->rowCount() > 0) {
			echo '<table border="1">'."\n".
			"<tr>
		    <th>Name</th>
		    <th>Headline</th>";
			$stmt = $pdo->query("SELECT first_name, last_name, headline, profile_id FROM Profile");
			while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
				echo("<tr><td>".htmlentities($row['first_name'])." ".htmlentities($row['last_name'])."</td><td>".htmlentities($row['headline'])."</td>");
			}
			echo '</table>';
		} 
}
?>
</div>
</body>