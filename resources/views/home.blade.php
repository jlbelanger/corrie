<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width,initial-scale=1">
		<meta name="description" content="Find connections between characters on Coronation Street.">
		<meta name="keywords" content="coronation street, corrie, family, relationships">
		<meta property="og:title" content="Corrieography">
		<meta property="og:description" content="Find connections between characters on Coronation Street.">
		<meta property="og:image" content="{{ url('/assets/img/share.jpg') }}">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<link rel="apple-touch-startup-image" href="{{ url('/assets/img/splash/apple-splash-2048-2732.png') }}" media="(device-width: 1024px) and (device-height: 1366px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)">
		<link rel="apple-touch-startup-image" href="{{ url('/assets/img/splash/apple-splash-1668-2388.png') }}" media="(device-width: 834px) and (device-height: 1194px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)">
		<link rel="apple-touch-startup-image" href="{{ url('/assets/img/splash/apple-splash-1536-2048.png') }}" media="(device-width: 768px) and (device-height: 1024px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)">
		<link rel="apple-touch-startup-image" href="{{ url('/assets/img/splash/apple-splash-1668-2224.png') }}" media="(device-width: 834px) and (device-height: 1112px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)">
		<link rel="apple-touch-startup-image" href="{{ url('/assets/img/splash/apple-splash-1620-2160.png') }}" media="(device-width: 810px) and (device-height: 1080px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)">
		<link rel="apple-touch-startup-image" href="{{ url('/assets/img/splash/apple-splash-1290-2796.png') }}" media="(device-width: 430px) and (device-height: 932px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)">
		<link rel="apple-touch-startup-image" href="{{ url('/assets/img/splash/apple-splash-1179-2556.png') }}" media="(device-width: 393px) and (device-height: 852px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)">
		<link rel="apple-touch-startup-image" href="{{ url('/assets/img/splash/apple-splash-1284-2778.png') }}" media="(device-width: 428px) and (device-height: 926px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)">
		<link rel="apple-touch-startup-image" href="{{ url('/assets/img/splash/apple-splash-1170-2532.png') }}" media="(device-width: 390px) and (device-height: 844px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)">
		<link rel="apple-touch-startup-image" href="{{ url('/assets/img/splash/apple-splash-1125-2436.png') }}" media="(device-width: 375px) and (device-height: 812px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)">
		<link rel="apple-touch-startup-image" href="{{ url('/assets/img/splash/apple-splash-1242-2688.png') }}" media="(device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)">
		<link rel="apple-touch-startup-image" href="{{ url('/assets/img/splash/apple-splash-828-1792.png') }}" media="(device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)">
		<link rel="apple-touch-startup-image" href="{{ url('/assets/img/splash/apple-splash-1242-2208.png') }}" media="(device-width: 414px) and (device-height: 736px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)">
		<link rel="apple-touch-startup-image" href="{{ url('/assets/img/splash/apple-splash-750-1334.png') }}" media="(device-width: 375px) and (device-height: 667px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)">
		<link rel="apple-touch-startup-image" href="{{ url('/assets/img/splash/apple-splash-640-1136.png') }}" media="(device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)">
		<title>Corrieography</title>
		<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
		<link rel="icon" type="image/png" sizes="32x32" href="/favicon.png">
		<link rel="icon" href="/favicon.ico">
		<link rel="stylesheet" href="/assets/css/style.min.css?20220909">
		<link rel="manifest" href="/manifest.json">
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

			<footer>
				<a href="https://github.com/jlbelanger/corrie" id="github">GitHub</a>
			</footer>
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
