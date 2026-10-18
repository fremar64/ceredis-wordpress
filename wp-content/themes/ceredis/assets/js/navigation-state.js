(() => {
	document.querySelectorAll('.wp-block-navigation').forEach((navigation) => {
		const toggle = navigation.querySelector('.wp-block-navigation__responsive-container-open');
		const panel = navigation.querySelector('.wp-block-navigation__responsive-container');

		if (!toggle || !panel) {
			return;
		}

		const syncExpandedState = () => {
			toggle.setAttribute('aria-expanded', panel.classList.contains('is-menu-open') ? 'true' : 'false');
		};

		syncExpandedState();
		new MutationObserver(syncExpandedState).observe(panel, {
			attributes: true,
			attributeFilter: ['class'],
		});
	});
})();
