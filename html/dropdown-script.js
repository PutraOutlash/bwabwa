// IKI DIGAE DROPDOWN DEK FORUM DIMAS JALOK NGENE

document.addEventListener('DOMContentLoaded', function () {
    const dropdownContainer = document.querySelector('.custom-dropdown-container');

    // Cek keberadaan elemen agar tidak error di halaman lain
    if (!dropdownContainer) return;

    const selectedValue = dropdownContainer.querySelector('.dropdown-selected-value');
    const optionsList = dropdownContainer.querySelector('.dropdown-options');
    const realSelect = dropdownContainer.querySelector('#realSortSelect');
    const sortForm = document.getElementById('sortForm');

    // 1. Toggle (Buka/Tutup) dropdown saat tombol diklik
    selectedValue.addEventListener('click', function (e) {
        e.stopPropagation(); // Mencegah klik tembus ke dokumen (agar tidak langsung menutup)
        optionsList.classList.toggle('show');
        selectedValue.classList.toggle('active');
    });

    // 2. Logika saat salah satu Opsi dipilih
    const options = optionsList.querySelectorAll('li');

    options.forEach(option => {
        option.addEventListener('click', function (e) {
            e.stopPropagation();

            // Ambil data value dan teks dari opsi yang diklik
            const value = this.getAttribute('data-value');
            const text = this.textContent;

            // Update Tampilan Teks di Tombol Utama
            // Kita set innerHTML agar teks berubah tapi ikon panah tetap ada
            selectedValue.innerHTML = `<span>${text}</span> <i class="fas fa-chevron-down dropdown-arrow"></i>`;

            // Update nilai pada <select> asli yang tersembunyi
            if (realSelect) {
                realSelect.value = value;
            }

            // Update styling: hapus kelas 'selected' dari semua, tambahkan ke yang diklik
            options.forEach(item => item.classList.remove('selected'));
            this.classList.add('selected');

            // Tutup dropdown setelah memilih
            optionsList.classList.remove('show');
            selectedValue.classList.remove('active');

            // Submit formulir secara otomatis untuk memuat ulang halaman dengan filter baru
            if (sortForm) {
                sortForm.submit();
            }
        });
    });

    // 3. Tutup dropdown jika user klik di SEMBARANG tempat di luar area dropdown
    document.addEventListener('click', function (event) {
        if (!dropdownContainer.contains(event.target)) {
            optionsList.classList.remove('show');
            selectedValue.classList.remove('active');
        }
    });
});