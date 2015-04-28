<?
require 'app.php';
?>
<!DOCTYPE html>
<html lang="pl">
<head>
	<meta charset="UTF-8">
	<title>LO5 Książka</title>
	<meta name="viewport" content="initial-scale=1">
	<link rel="stylesheet" href="assets/css/style.css"/>

	<link rel="stylesheet"
		  href="//fonts.googleapis.com/css?family=Lato:400,700,900,400italic,700italic&subset=latin,latin-ext"/>
	<link rel="stylesheet"
		  href="//fonts.googleapis.com/css?family=PT+Serif:400,700,400italic,700italic&subset=latin,latin-ext&subset=latin,latin-ext"/>

</head>
<body>

<div class="book-container">
	<main class="book typo">

<?= app::getContent() ?>


	</main>
</div>

</body>
</html>