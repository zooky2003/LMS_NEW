(function () {
  function smoothScrollTo(hash) {
    try {
      var target = document.querySelector(hash);
      if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    } catch (e) {}
  }

  function toast(message, type) {
    var existing = document.querySelector('.contact-toast');
    if (existing) existing.remove();
    var el = document.createElement('div');
    el.className = 'contact-toast';
    el.style.position = 'fixed';
    el.style.right = '16px';
    el.style.bottom = '16px';
    el.style.zIndex = '9999';
    el.style.padding = '12px 14px';
    el.style.borderRadius = '12px';
    el.style.backdropFilter = 'saturate(140%) blur(10px)';
    el.style.boxShadow = '0 10px 30px -12px rgba(2, 8, 23, 0.35)';
    el.style.color = type === 'error' ? '#991b1b' : '#065f46';
    el.style.border = '1px solid ' + (type === 'error' ? '#fecaca' : '#bbf7d0');
    el.style.background = type === 'error' ? 'rgba(254, 226, 226, 0.8)' : 'rgba(236, 253, 245, 0.85)';
    el.textContent = message;
    document.body.appendChild(el);
    setTimeout(function () { el.remove(); }, 3500);
  }

  function validate(form) {
    var name = form.querySelector('#full_name');
    var email = form.querySelector('#email');
    var phone = form.querySelector('#phone');
    var course = form.querySelector('#course');
    var consent = form.querySelector('#consent');
    if (!name.value.trim()) return { ok: false, msg: 'Please enter your full name.' };
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) return { ok: false, msg: 'Please enter a valid email.' };
    if (!/^[0-9+\-\s]{9,}$/.test(phone.value)) return { ok: false, msg: 'Please enter a valid phone/WhatsApp number.' };
    if (!course.value) return { ok: false, msg: 'Please select a course.' };
    if (!consent.checked) return { ok: false, msg: 'Please agree to be contacted.' };
    return { ok: true };
  }

  window.ContactPage = {
    handleSubmit: function (e) {
      e.preventDefault();
      var form = e.target;
      var check = validate(form);
      if (!check.ok) {
        toast(check.msg, 'error');
        return false;
      }
      // Simulate success for now. You can wire this to backend later.
      toast('Thanks! Our admissions team will contact you shortly.', 'success');
      form.reset();
      return false;
    }
  };

  document.addEventListener('click', function (e) {
    if (e.target.matches('a[href^="#"]')) {
      var href = e.target.getAttribute('href');
      if (href && href.length > 1) {
        smoothScrollTo(href);
      }
    }
  });
})();


