import '../scss/style.scss';
import Autocomplete from './autocomplete';
import onSubmit from './submit';
import showResults from './results';

Autocomplete.get({
	container: document.getElementById('p1-container'),
	id: 'p1',
	options: window.people,
	selected: window.selectedP1,
});

Autocomplete.get({
	container: document.getElementById('p2-container'),
	id: 'p2',
	options: window.people,
	selected: window.selectedP2,
});

document.body.classList.add('js');

document.body.addEventListener('keydown', (e) => {
	if (e.key === 'Escape') {
		Autocomplete.closeAll();
	}
});

if (window.response) {
	document.getElementById('search').classList.add('shrink');
	document.getElementById('results').classList.add('show');
	showResults(window.response);
}

document.getElementById('form').addEventListener('submit', onSubmit);
