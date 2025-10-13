// Small admin interactivity: toggle card details (public copy)
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.admin-card .card-header').forEach(header => {
    header.addEventListener('click', () => {
      const body = header.nextElementSibling;
      if (!body) return;
      body.style.display = body.style.display === 'none' ? 'block' : 'none';
    });
  });
});
