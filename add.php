<?php
session_start();

$pdo = new PDO('mysql:host=localhost;port=3306;dbname=misc', 'ljumsi', 'pw');
$stmt = $pdo->query("SELECT * FROM autos");

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ( ! isset($_SESSION['name']) ) {
    die('ACCESS DENIED');
}

if ( isset($_POST['Cancel']) ) {
    header('Location: index.php');
    exit();
}

for($i=1; $i<=9; $i++) {
    if ( ! isset($_POST['year'.$i]) ) continue;
    if ( ! isset($_POST['desc'.$i]) ) continue;
    $year = $_POST['year'.$i];
    $desc = $_POST['desc'.$i];
    if ( strlen($year) == 0 || strlen($desc) == 0 ) {
        $_SESSION['message'] = "All fields are required";
        $_SESSION['status'] = false;
        header('Location: add.php');
		return;
    }
    if ( ! is_numeric($year) ) {
        $_SESSION['message'] = "Position year must be numeric";
        $_SESSION['status'] = false;
        header('Location: add.php');
		return;
	}
}

if (isset( $_POST['Add'])) {
	if ((strlen($_POST['last_name'])<1) || (strlen($_POST['first_name'])<1) || (strlen($_POST['email'])<1) || (strlen($_POST['headline'])<1) ||
	 (strlen($_POST['summary'])<1)) {
		$_SESSION['message'] = "All values are required";
		$_SESSION['status'] = false;
		header('Location: add.php');
		return;
	} else {
		if (strpos($_POST['email'], '@') == false)  {
			$_SESSION['message'] = "Email address must contain @";
			$_SESSION['status'] = false;
			header('Location: add.php');
			return;
		} else {

			
			$stmt = $pdo->prepare('INSERT INTO Profile (user_id, first_name, last_name, email, headline, summary) VALUES ( :uid, :first_name, :last_name, :email, :headline, :summary)');
	    $stmt->execute(array(
	    	':uid' => $_SESSION['user_id'],
	        ':first_name' => $_POST['first_name'],
	        ':last_name' => $_POST['last_name'],
	        ':email' => $_POST['email'],
	        ':headline' => $_POST['headline'],
	        ':summary' => $_POST['summary'])
		
    );
		$profile_id = $pdo->lastInsertId();
		$rank = 1;
    	for($i=1; $i<=9; $i++) {
	        if ( ! isset($_POST['year'.$i]) ) continue;
	        if ( ! isset($_POST['desc'.$i]) ) continue;
	        $year = $_POST['year'.$i];
	        $desc = $_POST['desc'.$i];

	        $stmt = $pdo->prepare('INSERT INTO Position
	            (profile_id, rank, year, description)
	        VALUES ( :pid, :rank, :year, :desc)');
	        $stmt->execute(array(
	            ':pid' => $profile_id,
	            ':rank' => $rank,
	            ':year' => $year,
	            ':desc' => $desc)
	        );
	        $rank++;
    	}
	    	$_SESSION['message'] = "Profile added";
	    	$_SESSION['status'] = true;
		}
	}
}

if ( isset($_POST['Add']) ) {
	if ($_SESSION['status'] == true) {
		header('Location: index.php');
		}

	
}




?>

<!DOCTYPE html>
<html>
<head>
<?php require_once "bootstrap.php"; require_once "pdo.php";?>
<title>Andrew Jun Lin's Resume Registry</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
</head>
<body>
<div class="container">
<h1><?php echo("Adding Profile for ".htmlentities($_SESSION['name']))?></h1>
<?php



if ( isset($_SESSION['status'])){
	if ($_SESSION['status'] == false) {
    echo('<p style="color: red;">'.htmlentities($_SESSION['message'])."</p>\n");
    unset($_SESSION['status']);
    unset($_SESSION['message']);
	} 
}
	
?>
<form method="POST">
<p>
	First Name:
	<input name="first_name" size=60>
</p>
<p>
	Last Name:
	<input name="last_name" size=60>
</p>
<p>
	Email:
	<input name="email" size = 50>
</p>
<p>
	Headline:<br>
	<input name="headline", size=80>
</p>
<p>
	Summary:<br>
	<textarea name="summary", rows="4" cols="80"></textarea>
</p>
<p>
	Position: <input type="submit" id="addPos" value="+">
	<div id="position_fields">
	</div>
</p>
<input type="submit" name="Add" value="Add">
<input type="submit" name="Cancel" value="Cancel">
</form>
<script>

countPos = 0;

$(document).ready(function(){
    window.console && console.log('Document ready called');
    $('#addPos').click(function(event){
        event.preventDefault();
        if ( countPos >= 9 ) {
            alert("Maximum of nine position entries exceeded");
            return;
        }
        countPos++;
        window.console && console.log("Adding position "+countPos);
        $('#position_fields').append(
            '<div id="position'+countPos+'"> \
            <p>Year: <input type="text" name="year'+countPos+'" value="" /> \
            <input type="button" value="-" \
                onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
            <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
            </div>');
    });
});
</script>
</div>
</body>

