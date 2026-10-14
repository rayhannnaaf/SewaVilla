(() => {
  const $ = (s, r = document) => r.querySelector(s);
  const $$ = (s, r = document) => [...r.querySelectorAll(s)];
  const B = document.body;

  // web storage
  //local storage
  const store = {
    get: k => { try { return localStorage.getItem(k); } catch (e) { return null; } },
    set: (k, v) => { try { localStorage.setItem(k, v); } catch (e) {} }
  };

  // session
  const ss = {
    get: k => { try { return sessionStorage.getItem(k); } catch (e) { return null; } },
    set: (k, v) => { try { sessionStorage.setItem(k, v); } catch (e) {} }
  };

  // promise
  const wait = ms => new Promise(r => setTimeout(r, ms));

    // fetch + async
    // api dibawah hanya untuk testing
    const API = 'https://jsonplaceholder.typicode.com/posts';
    const kirim = async data => {
      const r = await fetch(API, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
      });
      if (!r.ok) throw new Error('HTTP ' + r.status);
      return r.json();
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

  // tampil sembunyi sandi (EVENT click)
  $$('.eye').forEach(b => b.addEventListener('click', () => {
    const i = b.previousElementSibling, s = i.type === 'password';
    i.type = s ? 'text' : 'password';
    b.innerHTML = '<i data-lucide="' + (s ? 'eye-off' : 'eye') + '"></i>';
    b.setAttribute('aria-label', s ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
    icons();
  }));

  // validasi form (EVENT submit), sekarang async
  const f = $('form[data-form]');
  if (f) f.addEventListener('submit', async e => {
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
    const btn = $('.btn', f), lbl = $('span', btn), asal = lbl.textContent;
    btn.classList.add('load');
    lbl.textContent = 'Memproses...';
    try {
      const email = $('input[type=email]', f).value.trim();
      const nama = reg ? $('[name=nama]', f).value.trim() : '';
      // Promise.all: kirim ke server dan tahan animasi minimal 600 ms, tunggu keduanya selesai
      await Promise.all([kirim({ email, nama }), wait(600)]);
      if (reg) {
        store.set('arus_name', nama);
        alert('Pendaftaran berhasil. Silakan masuk.'); // popup box 1: alert
        location.href = 'login.html';
      } else {
        store.set('arus_user', store.get('arus_name') || email.split('@')[0]);
        location.href = 'dashboard.html';
      }
    } catch (err) {
      // Promise ditolak atau jaringan gagal: kembalikan tombol, tampilkan pesan
      btn.classList.remove('load');
      lbl.textContent = asal;
      const w = $('.fld', f);
      w.classList.add('bad');
      $('.err', w).textContent = 'Gagal terhubung ke server. Coba lagi.';
    }
  });

  // dashboard
  if (B.dataset.page === 'dash') {
    const nm = store.get('arus_user') || 'Tamu';
    $$('[data-user]').forEach(e => e.textContent = nm);
    $$('[data-ini]').forEach(e => e.textContent = nm[0].toUpperCase());
    const out = $('[data-out]');
    out && out.addEventListener('click', e => {
      // POPUP BOX 2: confirm, batal = tetap di dashboard
      if (!confirm('Yakin ingin keluar?')) { e.preventDefault(); return; }
      try { localStorage.removeItem('arus_user'); } catch (x) {}
    });
    // EVENT input: pencarian tabel real time
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

  // filter properti, state disimpan di sessionStorage agar bertahan saat refresh
  const cards = $$('.pc');
  let ft = ss.get('arus_ft') || 'all', qq = ss.get('arus_q') || '';
  const run = () => {
    let n = 0;
    ss.set('arus_ft', ft);
    ss.set('arus_q', qq);
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
  // pulihkan filter tersimpan (hanya jika sebelumnya diubah)
  if (cards.length && (ft !== 'all' || qq)) {
    $$('.tabs button').forEach(x => x.classList.toggle('on', x.dataset.f === ft));
    if (sf) $('[name=lokasi]', sf).value = qq;
    run();
  }

  // buletin footer: fetch async
  const nb = $('.nl button');
  if (nb) nb.addEventListener('click', async () => {
    const i = $('.nl input'), ok = /^\S+@\S+\.\S+$/.test(i.value);
    if (!ok) { i.placeholder = 'Format email belum benar.'; return; }
    nb.disabled = true;
    try {
      await kirim({ email: i.value.trim() });
      i.value = '';
      i.placeholder = 'Terima kasih, sudah terdaftar.';
    } catch (err) {
      i.placeholder = 'Gagal terhubung, coba lagi.';
    }
    nb.disabled = false;
  });
})();