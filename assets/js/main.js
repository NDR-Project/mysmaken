/* Interaksi UI sederhana untuk portal FasilitasKu. */
document.addEventListener('DOMContentLoaded', () => {
  // Tampilkan/sembunyikan kata sandi pada halaman login.
  document.querySelectorAll('[data-toggle-password]').forEach((button) => {
    button.addEventListener('click', () => {
      const input = document.getElementById(button.dataset.togglePassword);
      if (!input) return;
      const show = input.type === 'password';
      input.type = show ? 'text' : 'password';
      button.innerHTML = show
        ? '<i class="fa-regular fa-eye-slash"></i>'
        : '<i class="fa-regular fa-eye"></i>';
      button.setAttribute('aria-label', show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
    });
  });

  // Hitung karakter deskripsi laporan.
  const description = document.getElementById('deskripsi');
  const counter = document.getElementById('char-count');
  if (description && counter) {
    const updateCount = () => { counter.textContent = description.value.length; };
    description.addEventListener('input', updateCount);
    updateCount();
  }

  // Tampilkan nama file yang dipilih dan beri peringatan jika file terlalu besar.
  const fileInput = document.getElementById('foto');
  const fileName = document.getElementById('file-name');
  if (fileInput && fileName) {
    fileInput.addEventListener('change', () => {
      const file = fileInput.files[0];
      if (!file) { fileName.textContent = 'Belum ada file dipilih'; return; }
      if (file.size > 5 * 1024 * 1024) {
        fileInput.value = '';
        fileName.textContent = 'File terlalu besar. Maksimal 5 MB.';
        fileName.classList.add('text-error');
        return;
      }
      fileName.textContent = file.name;
      fileName.classList.remove('text-error');
    });
  }

  // Menu sidebar untuk tampilan mobile.
  const menuButton = document.querySelector('[data-menu-toggle]');
  const sidebar = document.querySelector('.sidebar');
  if (menuButton && sidebar) {
    menuButton.addEventListener('click', () => sidebar.classList.toggle('sidebar-open'));
  }

  // Notifikasi ringan untuk demo. Server flash-message tetap lebih tepat untuk produksi.
  document.querySelectorAll('[data-dismiss-alert]').forEach((button) => {
    button.addEventListener('click', () => button.closest('.alert')?.remove());
  });

  // Pastikan file yang dipilih benar-benar bertipe gambar di sisi UI.
  const reportForm = document.getElementById('report-form');
  if (reportForm && fileInput) {
    reportForm.addEventListener('submit', (event) => {
      const file = fileInput.files[0];
      if (file && !['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
        event.preventDefault();
        window.alert('Format foto harus JPG, PNG, atau WEBP.');
      }
    });
  }
});