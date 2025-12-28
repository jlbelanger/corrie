import '../css/style.css';
import Autocomplete from './autocomplete.js';
import onSubmit from './submit.js';
import showResults from './results.js';

const people = JSON.parse(document.querySelector('[data-people]').value);

Autocomplete.get({
	container: document.getElementById('p1-container'),
	id: 'p1',
	options: people,
	selected: parseInt(document.getElementById('p1-value').value, 10),
});

Autocomplete.get({
	container: document.getElementById('p2-container'),
	id: 'p2',
	options: people,
	selected: parseInt(document.getElementById('p2-value').value, 10),
});

document.body.classList.add('js');

document.body.addEventListener('keydown', (e) => {
	if (e.key === 'Escape') {
		Autocomplete.closeAll();
	}
});

const response = JSON.parse(document.querySelector('[data-response]').value);
if (response) {
	document.getElementById('search').classList.add('shrink');
	document.getElementById('results').classList.add('show');
	showResults(response);
}

document.getElementById('form').addEventListener('submit', onSubmit);
