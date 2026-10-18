document.addEventListener('DOMContentLoaded', () => {
  const button = document.querySelector('.ceredis-back-to-top');

  if (!button) {
    return;
  }

  if (button.dataset.ceredisBackToTopInitialized) {
    return;
  }

  button.dataset.ceredisBackToTopInitialized = 'true';

  const toggleButton = () => {
    const shouldShow = window.scrollY > 420;
    button.classList.toggle('is-visible', shouldShow);
    button.setAttribute('aria-hidden', String(!shouldShow));
  };

  toggleButton();
  window.addEventListener('scroll', toggleButton, { passive: true });

  button.addEventListener('click', () => {
    const behavior = window.matchMedia('(prefers-reduced-motion: reduce)').matches
      ? 'instant'
      : 'smooth';

    window.scrollTo({ top: 0, behavior });
  });
});
