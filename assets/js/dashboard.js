// Dashboard behavior: greeting, editable titles, chart, simple calendar
(function(){
  const qs = (s, r=document) => r.querySelector(s);
  const qsa = (s, r=document) => Array.from(r.querySelectorAll(s));

  // Greeting with name from localStorage or query
  const url = new URL(window.location.href);
  const nameFromQuery = url.searchParams.get('name');
  let name = nameFromQuery || '';
  try { if (!name) name = localStorage.getItem('studentName') || ''; } catch {}
  if (!name) name = 'Student';
  qs('#welcomeName').textContent = name;
  const navUserName = qs('#navUserName');
  if (navUserName) navUserName.textContent = name;

  // Hover glow position for bento items
  qsa('.bento-item').forEach(card => {
    card.addEventListener('mousemove', (e) => {
      const rect = card.getBoundingClientRect();
      card.style.setProperty('--mx', e.clientX - rect.left + 'px');
      card.style.setProperty('--my', e.clientY - rect.top + 'px');
    });
  });

  // Simple Chart.js example
  const ctx = document.getElementById('perfChart');
  if (ctx && 'Chart' in window) {
    const data = {
      labels: ['Jan','Feb','Mar','Apr','May','Jun'],
      datasets: [{
        label: 'Score %',
        data: [55, 60, 58, 64, 68, 72],
        fill: true,
        tension: .35,
        borderWidth: 2,
        borderColor: '#7c3aed',
        backgroundColor: 'rgba(124,58,237,.15)',
        pointBackgroundColor: '#7c3aed',
      }]
    };
    new Chart(ctx, {
      type: 'line',
      data,
      options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { suggestedMin: 0, suggestedMax: 100, ticks: { stepSize: 20 } } }
      }
    });
  }

  // Tiny calendar placeholder
  const cal = qs('#calendar');
  if (cal) {
    const d = new Date();
    cal.textContent = d.toLocaleString(undefined, { month: 'long', year: 'numeric' }) + ' — upcoming classes will appear here.';
  }

  // Rolling calendar icon on scroll
  const roll = qs('.roll-icon');
  if (roll) {
    const update = () => {
      const ang = (window.scrollY % 360);
      roll.style.transform = `rotate(${ang}deg)`;
      requestAnimationFrame(update);
    };
    update();
  }

  // Account dropdown
  const acc = qs('#account');
  const accBtn = qs('#accountBtn');
  const menu = qs('#accountMenu');
  if (acc && accBtn && menu) {
    const email = (() => { try { return localStorage.getItem('studentEmail') || 'student@example.com'; } catch { return 'student@example.com'; } })();
    const ddName = qs('#ddName');
    const ddEmail = qs('#ddEmail');
    if (ddName) ddName.textContent = name;
    if (ddEmail) ddEmail.textContent = email;

    accBtn.addEventListener('click', () => {
      acc.classList.toggle('open');
    });
    document.addEventListener('click', (e) => {
      if (!acc.contains(e.target)) acc.classList.remove('open');
    });

    // Clear stored user data on logout
    const logoutLink = qs('.menu-item.logout');
    if (logoutLink) {
      logoutLink.addEventListener('click', () => {
        try {
          localStorage.removeItem('studentName');
          localStorage.removeItem('studentEmail');
        } catch {}
      });
    }
  }

  // Liquid glass background (canvas metaballs)
  (function initLiquidBg(){
    const holder = qs('#liquidBg');
    if (!holder) return;

    const dpr = Math.min(window.devicePixelRatio || 1, 2);
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    holder.appendChild(canvas);

    const palette = ['#5227FF', '#FF9FFC', '#B19EEF'];
    const blobs = [];
    const blobCount = 8;
    const speedBase = 12; // px/sec (scaled by canvas size)

    function rand(min, max){ return Math.random() * (max - min) + min; }

    function resize(){
      const rect = holder.getBoundingClientRect();
      canvas.width = Math.max(1, Math.floor(rect.width * dpr));
      canvas.height = Math.max(1, Math.floor(rect.height * dpr));
    }

    function initBlobs(){
      blobs.length = 0;
      for (let i=0;i<blobCount;i++){
        blobs.push({
          x: rand(0.1, 0.9),
          y: rand(0.1, 0.9),
          r: rand(0.12, 0.22), // radius relative to min(canvas)
          vx: rand(-1, 1) * 0.03,
          vy: rand(-1, 1) * 0.03,
          color: palette[i % palette.length]
        });
      }
    }

    function draw(ts){
      const w = canvas.width; const h = canvas.height;
      ctx.clearRect(0,0,w,h);
      ctx.globalCompositeOperation = 'lighter';
      const minDim = Math.min(w,h);

      for(const b of blobs){
        const cx = b.x * w;
        const cy = b.y * h;
        const rr = b.r * minDim;
        const grad = ctx.createRadialGradient(cx, cy, rr*0.2, cx, cy, rr);
        grad.addColorStop(0, b.color + 'E6'); // ~90% alpha
        grad.addColorStop(1, b.color + '00');
        ctx.fillStyle = grad;
        ctx.beginPath();
        ctx.arc(cx, cy, rr, 0, Math.PI*2);
        ctx.fill();
      }
      ctx.globalCompositeOperation = 'source-over';
    }

    let last = performance.now();
    let raf = null;
    let running = false;
    let visibleByIO = true; // IO sets this; default true when present
    let hovering = false;

    function step(now){
      if (!running) return;
      const dt = Math.min(0.05, (now - last)/1000);
      last = now;
      const w = canvas.width; const h = canvas.height;
      const scale = (Math.min(w,h) / 800) * (speedBase/12);
      for(const b of blobs){
        b.x += b.vx * dt * scale;
        b.y += b.vy * dt * scale;
        if (b.x < 0.05 || b.x > 0.95) b.vx *= -1;
        if (b.y < 0.05 || b.y > 0.95) b.vy *= -1;
      }
      draw(now);
      raf = requestAnimationFrame(step);
    }

    function start(){ if (!running){ running = true; last = performance.now(); raf = requestAnimationFrame(step);} }
    function stop(){ running = false; if (raf) cancelAnimationFrame(raf); raf = null; }
    function updateRunState(){ (visibleByIO && (hovering || ('ontouchstart' in window))) ? start() : stop(); }

    const onResize = () => { resize(); draw(performance.now()); };
    window.addEventListener('resize', onResize);

    const io = new IntersectionObserver((entries)=>{
      const e = entries[0];
      visibleByIO = !!(e && e.isIntersecting);
      updateRunState();
    }, {threshold:[0,0.01]});
    io.observe(holder);

    // Hover to enable animation
    const targetHover = document.body || holder;
    targetHover.addEventListener('mouseenter', ()=>{ hovering = true; updateRunState(); });
    targetHover.addEventListener('mouseleave', ()=>{ hovering = false; updateRunState(); });

    resize();
    initBlobs();
    updateRunState();
  })();
})();
