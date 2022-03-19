<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width,initial-scale=1">
		<meta property="og:title" content="Corrieography">
		<meta property="og:image" content="{{ url('/assets/img/share.jpg') }}">
		<title>Corrieography</title>
		<link rel="icon" href="{{ url('/favicon.svg') }}">
		<link rel="stylesheet" href="{{ url('/assets/css/style.min.css?20220319') }}">
	</head>
	<body>
		<main id="container">
			<header id="search">
				<h1 id="title">
					<a href="/" id="title-link">
						<img alt="Corrieography" height="68" id="title-img" src="/assets/img/title.svg" width="500">
					</a>
				</h1>

				<noscript>This site requires Javascript to be enabled.</noscript>

				<form id="form" method="get">
					<div class="search-field">
						<label class="search-label" for="p1">How is</label>
						<div id="p1-container"></div>
					</div>

					<div class="search-field">
						<label class="search-label" for="p2">related to</label>
						<div id="p2-container"></div>
					</div>

					<p id="search-button-container">
						<button id="search-button" type="submit" aria-controls="results-message">Go</button>
					</p>
				</form>
			</header>

			<article id="results">
				<div aria-live="polite" id="results-message" role="region"></div>
				<ol id="results-list"></ol>
				<div id="spinner" role="alert">Looking for a connection...</div>
			</article>
		</main>
		<script>
		window.people = {!! json_encode($people) !!};
		window.response = {!! json_encode($response) !!};
		window.selectedP1 = {!! json_encode($p1) !!};
		window.selectedP2 = {!! json_encode($p2) !!};
		</script>
		<script src="{{ url('/assets/js/functions.min.js?20220319') }}"></script>
	</body>
</html>
