document.getElementById('form').addEventListener('submit', (e) => {
	e.preventDefault();

	document.getElementById('search').classList.add('shrink');
	document.getElementById('results').classList.add('show');

	const resultsMessage = document.getElementById('results-message');
	resultsMessage.classList.remove('show');

	const resultsList = document.getElementById('results-list');
	resultsList.classList.remove('show');

	const transitionDuration = 300;
	const p1 = window.autocomplete.p1.elements.hidden.value;
	const p2 = window.autocomplete.p2.elements.hidden.value;
	if (!p1 || !p2) {
		showResults({ message: 'Please select two people.', path: [] });
		return;
	}
	const query = `?p1=${p1}&p2=${p2}`;
	const title = `${window.autocomplete.p1.elements.input.value} & ${window.autocomplete.p2.elements.input.value} | Corrieography`;
	document.title = title;
	window.history.pushState({}, title, query);

	setTimeout(() => {
		resultsMessage.innerText = '';
		resultsList.innerText = '';
		const $spinner = showSpinner();
		request(`/search${query}`, (response) => {
			hideSpinner($spinner);
			setTimeout(() => {
				response = JSON.parse(response);
				showResults(response);
			}, transitionDuration);
		});
	}, transitionDuration);
});
