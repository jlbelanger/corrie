export const showSpinner = () => {
	const $spinner = document.getElementById('spinner');
	$spinner.classList.add('show');
	return $spinner;
};

export const hideSpinner = ($spinner) => {
	$spinner.classList.remove('show');
};
