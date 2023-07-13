export default (url, callback) => {
	const req = new XMLHttpRequest();
	req.onreadystatechange = () => {
		if (req.readyState === XMLHttpRequest.DONE) {
			callback(req.responseText);
		}
	};
	req.open('GET', url, true);
	req.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
	req.send(null);
};
