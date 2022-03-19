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
