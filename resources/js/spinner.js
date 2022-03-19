function showSpinner() { // eslint-disable-line no-unused-vars
	const $spinner = document.getElementById('spinner');
	$spinner.classList.add('show');
	return $spinner;
}

function hideSpinner($spinner) { // eslint-disable-line no-unused-vars
	$spinner.classList.remove('show');
}
