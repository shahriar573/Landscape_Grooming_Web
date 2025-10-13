// Simple surprise: show confetti when .surprise-btn clicked (public copy)
document.addEventListener('DOMContentLoaded', function() {
  function confettiBurst() {
    const container = document.createElement('div');
    container.style.position = 'fixed';
    container.style.left = 0;
    container.style.top = 0;
    container.style.width = '100%';
    container.style.height = '100%';
    container.style.pointerEvents = 'none';
    document.body.appendChild(container);

    for (let i=0;i<40;i++){
      const el = document.createElement('div');
      el.style.position='absolute';
      el.style.left = Math.random()*100 + '%';
      el.style.top = Math.random()*50 + '%';
      el.style.width = el.style.height = (6 + Math.random()*10) + 'px';
      el.style.background = ['#f59e0b','#10b981','#06b6d4','#7c3aed'][Math.floor(Math.random()*4)];
      el.style.opacity = 0.95;
      el.style.borderRadius = '2px';
      el.style.transform = 'translateY(-200px) rotate(' + (Math.random()*360) + 'deg)';
      el.style.transition = 'transform 1200ms cubic-bezier(.2,.8,.2,1), opacity 1200ms';
      container.appendChild(el);
      setTimeout(()=>{
        el.style.transform = 'translateY(800px) rotate(' + (Math.random()*720) + 'deg)';
        el.style.opacity = 0;
      }, 20 + Math.random()*300);
    }
    setTimeout(()=>document.body.removeChild(container), 1600);
  }

  document.querySelectorAll('.surprise-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      confettiBurst();
    });
  });
});
