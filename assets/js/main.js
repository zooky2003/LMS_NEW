// Multi-step registration form interactions
(function () {
  const form = document.getElementById('regForm');
  if (!form) return;

  const steps = Array.from(form.querySelectorAll('.form-step'));
  const stepper = document.getElementById('stepper');
  const dots = stepper ? Array.from(stepper.querySelectorAll('.step')) : [];
  let idx = 0;

  const isEmail = (v) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v);

  function show(i) {
    idx = Math.max(0, Math.min(i, steps.length - 1));
    steps.forEach((s, k) => s.classList.toggle('active', k === idx));
    dots.forEach((d, k) => {
      d.classList.toggle('active', k === idx);
      d.classList.toggle('completed', k < idx);
    });
    updatePrevState();
  }

  function updatePrevState() {
    const current = steps[idx];
    const prevBtn = current.querySelector('[data-prev]');
    if (prevBtn) prevBtn.disabled = idx === 0;
  }

  function clearErrors(scope) {
    scope.querySelectorAll('.input').forEach((el) => el.classList.remove('error'));
    scope.querySelectorAll('.error-msg').forEach((m) => (m.textContent = ''));
  }

  function setError(input, message) {
    input.classList.add('error');
    const msg = input.parentElement.querySelector('.error-msg');
    if (msg) msg.textContent = message;
  }

  function validateStep(step) {
    clearErrors(step);
    let ok = true;
    const required = Array.from(step.querySelectorAll('[required]'));
    required.forEach((el) => {
      const val = (el.value || '').trim();
      if (!val) {
        ok = false;
        setError(el, 'Required');
        return;
      }
      if (el.type === 'email' && !isEmail(val)) {
        ok = false; setError(el, 'Enter a valid email');
      }
      if (el.id === 'password' && val.length < 6) {
        ok = false; setError(el, 'Min length is 6');
      }
      if (el.type === 'tel' && !/^[0-9+\-()\s]{7,}$/.test(val)) {
        ok = false; setError(el, 'Enter a valid number');
      }
    });
    return ok;
  }

  // Wire next/prev
  form.addEventListener('click', (e) => {
    const nextBtn = e.target.closest('[data-next]');
    const prevBtn = e.target.closest('[data-prev]');
    if (nextBtn) {
      const current = steps[idx];
      if (validateStep(current)) show(idx + 1);
    }
    if (prevBtn) {
      show(idx - 1);
    }
  });

  // Submit
  // Replace the old function with this new one:
form.addEventListener('submit', (e) => {
    // Validate all steps one last time before submitting
    for (let i = 0; i < steps.length; i++) {
        if (!validateStep(steps[i])) { 
            e.preventDefault(); // Stop submission ONLY if any step is invalid
            show(i);            // Show the user which step has an error
            return; 
        }
    }
    // If all steps are valid, the form will now submit normally to your PHP script.
});

  // Improve UX: pressing Enter moves to next step when valid
  form.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
      const isTextInput = ['INPUT', 'SELECT', 'TEXTAREA'].includes(document.activeElement.tagName);
      if (isTextInput) { e.preventDefault(); const s = steps[idx]; if (validateStep(s)) show(idx + 1); }
    }
  });

  // Initialize
  show(0);
})();
