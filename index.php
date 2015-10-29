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
			<h1>Spis elementów</h1>

			<h2>HTML</h2>
			<ul class="toc">
			<? foreach($data['elements']['html'] as $item): ?>
				<li href="#element-html-<?= $item ?>"><a href="#element-html-<?= $item ?>"><code>&lt;<?= $item ?>&gt;</code></a></li>
			<? endforeach; ?>
			</ul>


			<h2>CSS</h2>
			<ul class="toc">
			<? foreach($data['elements']['css'] as $item): ?>
				<li href="#element-css-<?= $item ?>"><a href="#element-css-<?= $item ?>"><code><?= $item ?></code></a></li>
			<? endforeach; ?>
			</ul>
		</section>
		<section class="menu">
			<h1>Spis treści</h1>
			<ul class="toc">
				<? foreach($data['menu'] as $item): ?>
					<? if($item['no'] <= 3): ?>
						<li href="#<?= $item['url'] ?>" class="<? if($item['no'] == 1): ?>strong<? endif; ?>"><a href="#<?= $item['url'] ?>"><?= str_repeat('&nbsp;', ($item['no'] - 1) * 7) ?>
							<?= $item['text'] ?>
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