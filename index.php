<?
require 'app.php';
$data = app::getContent();
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

		<?= $data['html'] ?>

		<section class="menu">
			<h1>Spis treści</h1>
			<ul class="toc">
				<? foreach($data['menu'] as $item): ?>
					<? if($item['no'] <= 3): ?>
						<li href="#<?= $item['url'] ?>"><a href="#<?= $item['url'] ?>"><?= str_repeat('&nbsp;', ($item['no'] - 1) * 7) ?>
						<? if($item['no'] == 1): ?>
							<strong>
						<? endif; ?>
						<?= $item['text'] ?>
						<? if($item['no'] == 1): ?>
							</strong>
						<? endif; ?>
						</a>
						</li>
					<? endif; ?>
				<? endforeach; ?>
			</ul>
		</section>

	</main>

</div>

</body>
</html>