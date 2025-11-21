// File: app.js 

document.addEventListener('DOMContentLoaded', (event) => {

  // ================================================================
  // BAGIAN 1: KODE UNTUK DARK MODE (1 TOMBOL TOGGLE)
  // ================================================================

  const toggleButton = document.getElementById('darkModeToggle');
  const body = document.body;

  // Fungsi untuk menerapkan tema
  function applyTheme(theme) {
    if (theme === 'dark') {
      body.classList.add('dark-mode');
    } else {
      body.classList.remove('dark-mode');
    }
  }

  // 1. Cek tema yang tersimpan di localStorage saat halaman dimuat
  let savedTheme = localStorage.getItem('theme');

  if (!savedTheme) {
    // Jika tidak ada, cek preferensi sistem (default)
    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
      savedTheme = 'dark';
    } else {
      savedTheme = 'light';
    }
  }

  // Terapkan tema yang sudah ditentukan
  applyTheme(savedTheme);


  // 2. Tambahkan event listener ke tombol toggle
  if (toggleButton) {
    toggleButton.addEventListener('click', () => {
      // Balikkan (toggle) kelas 'dark-mode'
      body.classList.toggle('dark-mode');

      // Simpan pilihan baru ke localStorage
      if (body.classList.contains('dark-mode')) {
        localStorage.setItem('theme', 'dark');
      } else {
        localStorage.setItem('theme', 'light');
      }
    });
  }

  // =======================================================
  // --- BAGIAN 2: KODE FAQ (LOGIKA KLIK) ---
  // =======================================================

  const faqQuestions = document.querySelectorAll('.faq-question');

  faqQuestions.forEach(question => {
    question.addEventListener('click', () => {
      const item = question.closest('.faq-item');
      if (item) {
        item.classList.toggle('open');
      }
    });
  });

});