export default class Autocomplete {
	constructor({ container, id, options, selected }) {
		this.elements = {
			container,
			close: null,
			hidden: null,
			input: null,
			list: null,
		};
		this.id = id;
		this.options = options;
		this.filteredOptions = options;
		this.selectedIndex = 0;
		this.initialId = selected;
		this.currentLabel = '';
		this.open = false;
		this.isCloseVisible = false;
		this.init();
	}

	static get(options) {
		const a = new Autocomplete(options);
		window.autocomplete = window.autocomplete || {};
		window.autocomplete[options.id] = a;
		return a;
	}

	init() {
		// Update container.
		this.elements.container.setAttribute('class', 'select');
		this.elements.container.addEventListener('focusout', Autocomplete.onFocusOut);

		// Add text input.
		this.elements.input = document.createElement('input');
		this.elements.input.setAttribute('autocomplete', 'off');
		this.elements.input.setAttribute('class', 'select-input');
		this.elements.input.setAttribute('id', this.id);
		this.elements.input.setAttribute('spellcheck', 'false');
		this.elements.input.setAttribute('type', 'text');
		if (this.initialId) {
			const selectedOption = this.options.find(({ value }) => value === this.initialId);
			if (selectedOption) {
				this.elements.input.value = selectedOption.label;
				this.currentLabel = selectedOption.label;
			}
		}
		this.elements.input.addEventListener('focus', Autocomplete.onFocusInput);
		this.elements.input.addEventListener('mousedown', Autocomplete.onClickInput);
		this.elements.input.addEventListener('keydown', Autocomplete.onKeydownInput);
		this.elements.input.addEventListener('keyup', Autocomplete.onKeyupInput);
		this.elements.container.appendChild(this.elements.input);

		// Add close button.
		this.elements.close = document.createElement('button');
		this.elements.close.setAttribute('aria-label', 'Close');
		this.elements.close.setAttribute('class', 'select-close');
		this.elements.close.setAttribute('data-id', this.id);
		this.elements.close.setAttribute('type', 'button');
		this.elements.close.addEventListener('click', Autocomplete.onClickClose);
		if (this.currentLabel) {
			this.showClose();
		}
		this.elements.container.appendChild(this.elements.close);

		// Add results list.
		this.elements.list = document.createElement('ul');
		this.elements.list.setAttribute('class', 'select-list');
		this.elements.list.setAttribute('tabindex', '-1');
		this.elements.container.appendChild(this.elements.list);

		// Add hidden input.
		this.elements.hidden = document.createElement('input');
		this.elements.hidden.setAttribute('class', 'select-hidden');
		this.elements.hidden.setAttribute('name', this.id);
		this.elements.hidden.setAttribute('type', 'hidden');
		if (this.initialId) {
			this.elements.hidden.setAttribute('data-value', this.currentLabel);
			this.elements.hidden.value = this.initialId;
		}
		this.elements.container.appendChild(this.elements.hidden);

		// Add options to results list.
		this.setOptions();

		// Set the initially selected item.
		const initialItem = this.elements.list.querySelector(`[value="${this.initialId}"]`);
		if (initialItem) {
			initialItem.classList.add('selected');
			this.selectedIndex = initialItem.getAttribute('data-index');
			initialItem.scrollIntoView({ block: 'nearest' });
		}
	}

	setOptions(search = '') {
		// Filter options by current search.
		this.filteredOptions = this.options;
		if (search) {
			const cleanSearch = search.toLowerCase().trim();
			this.filteredOptions = this.filteredOptions.filter(({ label }) => (
				label.toLowerCase().match(new RegExp(`(^|[^a-z])${cleanSearch}`))
			));
		}

		// Empty list.
		this.elements.list.innerText = '';

		// Add items to list.
		this.filteredOptions.forEach(({ value, label }, index) => {
			const item = document.createElement('li');
			item.setAttribute('class', 'select-item');

			const button = document.createElement('button');
			button.setAttribute('class', 'select-button');
			button.setAttribute('data-index', index);
			button.setAttribute('data-name', this.id);
			button.setAttribute('tabindex', '-1');
			button.setAttribute('type', 'button');
			button.value = value;
			button.addEventListener('click', Autocomplete.onClickItem);
			button.addEventListener('mouseenter', Autocomplete.onMouseenterItem);
			button.innerText = label;
			item.appendChild(button);

			this.elements.list.appendChild(item);
		});
	}

	hideList() {
		if (this.open) {
			this.elements.list.classList.remove('show');
			this.open = false;
		}
	}

	showList() {
		if (!this.open) {
			this.elements.list.classList.add('show');
			this.open = true;
		}
	}

	hideClose() {
		if (this.isCloseVisible) {
			this.elements.close.classList.remove('show');
			this.isCloseVisible = false;
		}
	}

	showClose() {
		if (!this.isCloseVisible) {
			this.elements.close.classList.add('show');
			this.isCloseVisible = true;
		}
	}

	setSelectedIndex(i) {
		this.selectedIndex = parseInt(i, 10);

		// Unhighlight currently selected item.
		const selected = this.elements.list.querySelector('.selected');
		if (selected) {
			selected.classList.remove('selected');
		}

		// Highlight new item.
		const item = this.elements.list.querySelector(`[data-index="${i}"]`);
		if (item) {
			item.classList.add('selected');
			item.scrollIntoView({ block: 'nearest' });
		}
	}

	clearInput() {
		this.currentLabel = '';
		this.elements.input.value = '';
		this.elements.hidden.value = '';
		this.elements.hidden.setAttribute('data-value', '');
		this.setOptions();
		this.setSelectedIndex(0);
	}

	setValue(button) {
		this.currentLabel = button.innerText;
		this.elements.input.value = button.innerText;
		this.elements.hidden.value = button.value;
		this.elements.hidden.setAttribute('data-value', button.innerText);
		this.elements.input.focus();
		this.hideList();
	}

	static onKeydownInput(e) {
		const id = e.target.getAttribute('id');
		const a = window.autocomplete[id];
		if (e.key === 'ArrowDown') {
			a.showList();
			if (a.selectedIndex >= (a.filteredOptions.length - 1)) {
				a.setSelectedIndex(0);
			} else {
				a.setSelectedIndex(a.selectedIndex + 1);
			}
		} else if (e.key === 'ArrowUp') {
			a.showList();
			if (a.selectedIndex > 0) {
				a.setSelectedIndex(a.selectedIndex - 1);
			} else {
				a.setSelectedIndex(a.filteredOptions.length - 1);
			}
		} else if (e.key === 'Enter') {
			if (a.open) {
				e.preventDefault();
				const button = a.elements.list.querySelector('.selected');
				if (button) {
					a.setValue(button);
				}
			}
		}
	}

	static onKeyupInput(e) {
		const id = e.target.getAttribute('id');
		const a = window.autocomplete[id];
		if (e.target.value.trim() !== a.currentLabel.trim()) {
			a.showList();
			a.currentLabel = e.target.value;
			a.setOptions(a.currentLabel);
			a.setSelectedIndex(0);
		}
	}

	static onFocusOut(e) {
		const leaveFocus = e.target.closest('.select');
		const gainFocus = e.relatedTarget ? e.relatedTarget.closest('.select') : null;
		if (gainFocus && leaveFocus.getAttribute('id') === gainFocus.getAttribute('id')) {
			return;
		}

		const input = leaveFocus.querySelector('.select-input');
		const id = input.getAttribute('id');
		const a = window.autocomplete[id];
		if (input.value === a.elements.hidden.getAttribute('data-value')) {
			a.hideList();
			if (a.currentLabel === '') {
				a.hideClose();
			}
		} else {
			a.clearInput();
			a.hideList();
			a.hideClose();
		}
	}

	static onFocusInput(e) {
		const id = e.target.getAttribute('id');
		const a = window.autocomplete[id];
		a.showList();
		a.showClose();
	}

	static onClickInput(e) {
		const id = e.target.getAttribute('id');
		const a = window.autocomplete[id];
		if (a.open) {
			a.hideList();
			if (a.currentLabel === '') {
				a.hideClose();
			}
		} else {
			a.showList();
			a.showClose();
		}
	}

	static onClickItem(e) {
		const button = e.target;
		const id = button.getAttribute('data-name');
		const a = window.autocomplete[id];
		a.setValue(button);
		a.setSelectedIndex(button.getAttribute('data-index'));
	}

	static onMouseenterItem(e) {
		const button = e.target;
		const id = button.getAttribute('data-name');
		const a = window.autocomplete[id];
		a.setSelectedIndex(button.getAttribute('data-index'));
	}

	static onClickClose(e) {
		const id = e.target.getAttribute('data-id');
		const a = window.autocomplete[id];
		if (!a.open) {
			a.clearInput();
		}
		a.hideList();
		if (a.currentLabel === '') {
			a.hideClose();
		}
	}

	static closeAll() {
		Object.values(window.autocomplete).forEach((a) => {
			a.hideList();
		});
	}
}
