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

		const div = document.createElement('div');
		div.setAttribute('class', 'result-image');
		li.appendChild(div);

		const image = document.createElement('img');
		image.setAttribute('class', `result-img ${response.path[i].status}`);
		image.setAttribute('src', response.path[i].image ? response.path[i].image : '/assets/img/user.png');
		div.appendChild(image);

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
