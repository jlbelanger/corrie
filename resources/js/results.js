function showResults(response) {
	document.getElementById('results').classList.add('show');

	// Set message.
	const resultsMessage = document.getElementById('results-message');
	resultsMessage.innerText = response.message;

	// Set people.
	const num = response.path.length;
	const resultsList = document.getElementById('results-list');
	resultsList.innerText = '';
	let i;
	for (i = 0; i < num; i += 1) {
		const li = document.createElement('li');
		li.setAttribute('class', 'result-item');

		const image = document.createElement('div');
		image.setAttribute('class', `result-image ${response.path[i].status}`);
		image.style.backgroundImage = response.path[i].image ? `url("${response.path[i].image}")` : 'url("/assets/img/user.png")';
		li.appendChild(image);

		const name = document.createElement('div');
		name.setAttribute('class', 'result-name');
		name.innerText = response.path[i].name;
		li.appendChild(name);

		resultsList.appendChild(li);
	}

	setTimeout(() => {
		resultsMessage.classList.add('show');
		resultsList.classList.add('show');
	}, 300);
}

if (window.response) {
	document.getElementById('search').classList.add('shrink');
	document.getElementById('results').classList.add('show');
	showResults(window.response);
}
