(() => {
  const $ = (s, r = document) => r.querySelector(s);
  const $$ = (s, r = document) => [...r.querySelectorAll(s)];
  const B = document.body;
  const store = {
    get: k => { try { return localStorage.getItem(k); } catch (e) { return null; } },
    set: (k, v) => { try { localStorage.setItem(k, v); } catch (e) {} }
  };
  const icons = () => window.lucide && lucide.createIcons();
  icons();

  // hitung naik
  function count(el) {
    const t = +el.dataset.count, s = el.dataset.suf || '', t0 = performance.now();
    (function f(n) {
      const p = Math.min((n - t0) / 1100, 1), v = Math.round(t * (1 - Math.pow(1 - p, 3)));
      el.textContent = (el.dataset.pre || '') + v.toLocaleString('id-ID') + s;
      if (p < 1) requestAnimationFrame(f);
    })(t0);
  }

  // muncul saat scroll
  $$('.bars').forEach(b => $$('i', b).forEach((e, i) => e.style.setProperty('--i', i)));
  const io = new IntersectionObserver(es => es.forEach(e => {
    if (!e.isIntersecting) return;
    e.target.classList.add('in');
    $$('[data-count]', e.target).forEach(count);
    io.unobserve(e.target);
  }), { threshold: .15 });
  $$('.rv').forEach(e => io.observe(e));

  // menu mobile dan sidebar
  const tog = v => {
    B.classList.toggle('nav-open', v);
    const b = $('.burger,.menu');
    b && b.setAttribute('aria-expanded', B.classList.contains('nav-open'));
  };
  $$('.burger,.menu').forEach(b => b.addEventListener('click', () => tog()));
  $$('.links a').forEach(a => a.addEventListener('click', () => tog(false)));
  const sc = $('.scrim');
  sc && sc.addEventListener('click', () => tog(false));
  document.addEventListener('keydown', e => e.key === 'Escape' && tog(false));

  // tampil sembunyi sandi
  $$('.eye').forEach(b => b.addEventListener('click', () => {
    const i = b.previousElementSibling, s = i.type === 'password';
    i.type = s ? 'text' : 'password';
    b.innerHTML = '<i data-lucide="' + (s ? 'eye-off' : 'eye') + '"></i>';
    b.setAttribute('aria-label', s ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
    icons();
  }));

  // validasi form
  const f = $('form[data-form]');
  if (f) f.addEventListener('submit', e => {
    e.preventDefault();
    let ok = true;
    const reg = f.dataset.form === 'register';
    $$('.fld', f).forEach(w => {
      const i = $('input', w), v = i.value.trim();
      let m = '';
      if (i.type === 'checkbox') { if (!i.checked) m = 'Centang dulu untuk lanjut.'; }
      else if (!v) m = 'Kolom ini wajib diisi.';
      else if (i.type === 'email' && !/^\S+@\S+\.\S+$/.test(v)) m = 'Format email belum benar.';
      else if (reg && i.name === 'pw' && v.length < 8) m = 'Minimal 8 karakter.';
      else if (i.name === 'pw2' && v !== $('[name=pw]', f).value) m = 'Kata sandi tidak sama.';
      w.classList.toggle('bad', !!m);
      $('.err', w).textContent = m;
      if (m) ok = false;
    });
    if (!ok) {
      f.classList.remove('shake'); void f.offsetWidth; f.classList.add('shake');
      $('.bad input', f).focus();
      return;
    }
    const btn = $('.btn', f);
    btn.classList.add('load');
    $('span', btn).textContent = 'Memproses...';
    setTimeout(() => {
      if (reg) { store.set('arus_name', $('[name=nama]', f).value.trim()); location.href = 'login.html'; }
      else {
        const em = $('[name=email]', f).value.trim().split('@')[0];
        store.set('arus_user', store.get('arus_name') || em);
        location.href = 'dashboard.html';
      }
    }, 900);
  });

  // dashboard
  if (B.dataset.page === 'dash') {
    const nm = store.get('arus_user') || 'Tamu';
    $$('[data-user]').forEach(e => e.textContent = nm);
    $$('[data-ini]').forEach(e => e.textContent = nm[0].toUpperCase());
    const out = $('[data-out]');
    out && out.addEventListener('click', () => { try { localStorage.removeItem('arus_user'); } catch (e) {} });
    const q = $('#q');
    q && q.addEventListener('input', () => {
      const v = q.value.toLowerCase().trim();
      let n = 0;
      $$('tbody tr').forEach(r => {
        const h = !r.textContent.toLowerCase().includes(v);
        r.hidden = h;
        if (!h) n++;
      });
      $('.empty').style.display = n ? 'none' : 'block';
    });
  }

  // header bayangan saat scroll
  const nv = $('.nav');
  if (nv) addEventListener('scroll', () => nv.classList.toggle('scrolled', scrollY > 8), { passive: true });

  // filter properti
  const cards = $$('.pc');
  let ft = 'all', qq = '';
  const run = () => {
    let n = 0;
    cards.forEach(c => {
      const ok = (ft === 'all' || c.dataset.type === ft) && c.dataset.loc.includes(qq);
      c.hidden = !ok;
      if (ok) n++;
    });
    const e = $('.props .empty');
    if (e) e.style.display = n ? 'none' : 'block';
  };
  $$('.tabs button').forEach(b => b.addEventListener('click', () => {
    $$('.tabs button').forEach(x => x.classList.remove('on'));
    b.classList.add('on');
    ft = b.dataset.f;
    run();
  }));
  const sf = $('.srch-f');
  if (sf) sf.addEventListener('submit', e => {
    e.preventDefault();
    qq = $('[name=lokasi]', sf).value.toLowerCase().trim();
    run();
    $('#properti').scrollIntoView({ behavior: 'smooth' });
  });

  // buletin footer
  const nb = $('.nl button');
  if (nb) nb.addEventListener('click', () => {
    const i = $('.nl input'), ok = /^\S+@\S+\.\S+$/.test(i.value);
    if (ok) i.value = '';
    i.placeholder = ok ? 'Terima kasih, sudah terdaftar.' : 'Format email belum benar.';
  });
})();
