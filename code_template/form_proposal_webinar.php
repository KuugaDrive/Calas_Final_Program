<?php
session_start();
if (!isset($_SESSION['logged_in']) || !in_array($_SESSION['role'], ['asisten_lab', 'spv'])) {
    header('Location: login.php');
    exit;
}

$nama_pantia = [
    "Adrian Dzariat Maulana", "Ahmad Taufan Hidayat", "Bima Rasta Guvara", "Farand Effraim Karamoy",
    "Fauzi Alfadhillah", "Fredy Dwi Saputra", "Izhar Fahmi", "Jeneri Bayu Nugroho", "Kurnia Rahmawati",
    "Mochamad Rhiezky Eka Putra", "Muhammad Rifqi Fauzan", "Putri Novita Sari", "Rafif Ali Rachman",
    "Rayvano Putra Pratama", "Zidane Putra Waluyo","Ahvaz Haidar Gaza Wahyudi","Aktar Faizil","Astri Ananta Kartini",
    "Davi Rizky Madani","Inaya Zehan Kalyzta","Muhammad Rafif Rabbani","Muhammad Razin Haidar Karim",
    "Muhammad Rfiky Ramadhani","Naufa Aulia Sabila Azzahra","Rizky Saputra","Shandyka Azmi","Syukranda"
];

$jumlah_anggota_per_divisi = 7;

function input_field($label, $name, $type = 'text', $placeholder = '', $required = true, $value = '', $notes = '', $readonly = false) {
    global $nama_pantia;

    $req_attr = $required ? 'required' : '';
    $read_attr = $readonly ? 'readonly' : '';
    $note_html = $notes ? "<small class='form-text text-muted'>{$notes}</small>" : '';
    echo "<div class='mb-3'>
            <label for='{$name}' class='form-label'>{$label}" . ($required ? " <span class='text-danger'>*</span>" : "") . "</label>";

    if ($type === 'select' && !$readonly) {
        echo "<select class='form-select nama-anggota' id='{$name}' name='{$name}' {$req_attr}>";
        echo "<option value=''>-- Pilih Nama --</option>";
        foreach ($nama_pantia as $nama) {
            $selected = ($value === $nama) ? "selected" : "";
            echo "<option value='".htmlspecialchars($nama)."' $selected>".htmlspecialchars($nama)."</option>";
        }
        echo "</select>";
    } elseif ($type === 'textarea') {
        echo "<textarea class='form-control' id='{$name}' name='{$name}' rows='3' placeholder='{$placeholder}' {$req_attr}>".htmlspecialchars($value)."</textarea>";
    } else {
        // Untuk input NIM, batasi hanya 10 digit angka, pakai type=text supaya bisa pakai oninput filter
        if (($name === 'NIM_Ketua_Pelaksana_Form' || $name === 'NIM_PJ_Form') && $type === 'text') {
            echo "<input type='text' class='form-control' id='{$name}' name='{$name}' placeholder='{$placeholder}' maxlength='10' pattern='\\d{10}' oninput='this.value = this.value.replace(/[^0-9]/g, \"\")' title='Harus 10 digit angka' {$req_attr} />";
        } else {
            echo "<input type='{$type}' class='form-control' id='{$name}' name='{$name}' placeholder='{$placeholder}' value='".htmlspecialchars($value)."' {$req_attr} {$read_attr} />";
        }
    }

    echo "{$note_html}</div>";
}

function textarea_field($label, $name, $rows = 3, $placeholder = '', $required = true, $value = '', $notes = '') {
    $req_attr = $required ? 'required' : '';
    $note_html = $notes ? "<small class='form-text text-muted'>{$notes}</small>" : '';
    echo "<div class='mb-3'>
            <label for='{$name}' class='form-label'>{$label}" . ($required ? " <span class='text-danger'>*</span>" : "") . "</label>
            <textarea class='form-control' id='{$name}' name='{$name}' rows='{$rows}' placeholder='{$placeholder}' {$req_attr}>".htmlspecialchars($value)."</textarea>
            {$note_html}
          </div>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Form Proposal Webinar</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body { margin: 0; font-family: 'Segoe UI', sans-serif; background-color: #f1f2f6; }
    .wrapper { display: flex; min-height: 100vh; }
    .sidebar { width: 260px; background-color: #2c3e50; color: white; padding: 20px; flex-shrink: 0; box-shadow: 2px 0 5px rgba(0,0,0,0.1); }
    .sidebar img.logo { width: 80%; max-width: 150px; display: block; margin: 0 auto 25px auto; }
    .sidebar a, .sidebar .dropdown .dropdown-btn {
      display: block; padding: 12px 15px; color: #ecf0f1; text-decoration: none; margin-bottom: 8px;
      border-radius: 5px; font-size: 15px; transition: background-color 0.3s ease, color 0.3s ease;
    }
    .sidebar a.active, .sidebar a:hover, .sidebar .dropdown .dropdown-btn:hover {
      background-color: #3498db; color: white;
    }
    .sidebar .dropdown .dropdown-btn { background: none; border: none; text-align: left; width: 100%; cursor: pointer; }
    .dropdown-content { display: none; padding-left: 20px; margin-top: -5px; margin-bottom: 8px; }
    .dropdown-content a { font-size: 0.9em; padding: 8px 15px; color: #bdc3c7; }
    .dropdown-content a:hover, .dropdown-content a.active { color: white; background-color: #2980b9; }
    .main-content { flex-grow: 1; padding: 30px 40px; overflow-y: auto; }
    .content-wrapper { max-width: 900px; margin: 0 auto; background-color: #fff; padding: 30px 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
    h2.form-title { text-align: center; margin-bottom: 30px; color: #2c3e50; font-weight: 600; }
    .form-label { font-weight: 500; color: #34495e; }
    .form-control, .form-select { border-radius: 5px; border-color: #dfe4ea; }
    .form-control:focus { border-color: #3498db; box-shadow: 0 0 0 0.2rem rgba(52,152,219,0.25); }
    .btn-submit-custom { background-color: #3498db; border-color: #3498db; color: white; padding: 10px 25px; font-size: 16px; border-radius: 5px; transition: background-color 0.3s ease; }
    .btn-submit-custom:hover { background-color: #2980b9; border-color: #2980b9; color: white; }

    .modal-custom {
  display: none;
  position: fixed;
  z-index: 9999;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto;
  background-color: rgba(0,0,0,0.4);
}

.modal-content-custom {
  background-color: #ffffff;
  margin: 10% auto;
  padding: 30px;
  border: none;
  border-radius: 12px;
  width: 400px;
  text-align: center;
  box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}

  </style>
</head>
<body>
  <div class="wrapper">
    <nav class="sidebar">
      <img src="../src/img/logo_lab.png" alt="Logo Lab ICT" class="logo" />
      <a href="../src/dashboard_user.php">Dashboard</a>
      <a href="../src/proposal.php" class="active">Proposal</a>
      <a href="../src/lpj.php">LPJ</a>
      <a href="../src/sertifikat.php">Sertifikat</a>
      <a href="../src/banner.php">Banner</a>
      <a href="../src/dokumentasi.php">Dokumentasi</a>
      <div class="dropdown">
        <button class="dropdown-btn">Buat Berdasarkan Template</button>
        <div class="dropdown-content">
          <div class="dropdown-submenu">
            <button class="dropdown-btn">Proposal</button>
            <div class="dropdown-content">
              <a href="../code_template/form_proposal_webinar.php">Webinar</a>
              <a href="../code_template/form_proposal_workshop.php">Workshop</a>
              <a href="../code_template/form_proposal_talkshow.php">Talkshow</a>
              <a href="../code_template/form_proposal_bukber.php">Bukber</a>
            </div>
          </div>
          <div class="dropdown-submenu">
            <button class="dropdown-btn">LPJ</button>
            <div class="dropdown-content">
              <a href="../code_template/form_lpj_webinar.php">Webinar</a>
              <a href="../code_template/form_lpj_workshop.php">Workshop</a>
              <a href="../code_template/form_lpj_talkshow.php">Talkshow</a>
              <a href="../code_template/form_lpj_bukber.php">Bukber</a>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <main class="main-content">
      <div class="content-wrapper">
        <h2 class="form-title">Form Input Proposal Lengkap</h2>

        <form method="POST" action="generate_proposal_webinar.php" enctype="multipart/form-data" target="hidden_iframe" onsubmit="return validateFormClientSide() && onSubmitForm()">
          <?php
          input_field('Nama Kegiatan', 'Nama_Kegiatan', 'text', 'Contoh: Webinar Series Digital Marketing 2024');
          input_field('Judul Kegiatan', 'Judul_Kegiatan', 'text', 'Contoh: Strategi Jitu Pemasaran Digital di Era AI');
          input_field('Periode Asisten Lab', 'Periode_Aslab', 'text', 'Contoh: Genap 2023/2024');
          input_field('Tahun Akademik', 'Tahun_Akademik', 'number', 'Contoh: 2024');
          input_field('Waktu Pelaksanaan', 'Waktu_Pelaksanaan', 'text', 'Contoh: Sabtu, 20 Juli 2024, Pukul 09:00 - 12:00 WIB');
          input_field('Tempat Kegiatan', 'Tempat_Kegiatan', 'text', 'Contoh: Zoom Meeting');
          input_field('Total Estimasi Biaya (Rp)', 'Total_Biaya', 'number', 'Contoh: 1500000', true, '', 'Masukkan angka saja, tanpa titik atau koma.');

          input_field('Unggah TTD Ketua Pelaksana', 'TTD_Ketua_Pelaksana', 'file', '', true, '', 'Format: .png, .jpg, .jpeg. Maks: 1MB');
          input_field('Unggah TTD Penanggung Jawab', 'TTD_Penanggung_Jawab', 'file', '', true, '', 'Format: .png, .jpg, .jpeg. Maks: 1MB');
          input_field('Tanggal Pembuatan Proposal (Untuk Surat Pengesahan)', 'Tanggal_Penulisan_Proposal', 'date');

          input_field('Nama Ketua Pelaksana (Form)', 'Nama_Ketua_Pelaksana_Form', 'select');
          input_field('NIM Ketua Pelaksana (Form)', 'NIM_Ketua_Pelaksana_Form', 'text', 'Contoh: 2412501914', true, '', 'Harus 10 digit angka');
          input_field('Nama Penanggung Jawab (Form)', 'Nama_PJ_Form', 'select');
          input_field('NIM Penanggung Jawab (Form)', 'NIM_PJ_Form', 'text', 'Contoh: 2311500546', true, '', 'Harus 10 digit angka');

          input_field('Tempat dan Tanggal Surat Ditulis', 'Tempat_Tanggal_Surat_Ditulis', 'text', 'Contoh: Jakarta, 30 Mei 2024');
          input_field('Nama Penulis Proposal (Yang tertera di lembar pengesahan)', 'Penulis_Proposal');

          textarea_field('Latar Belakang', 'Latar_Belakang', 5, 'Jelaskan alasan dan urgensi kegiatan ini.');
          textarea_field('Tujuan Kegiatan', 'Tujuan_Kegiatan', 4, 'Sebutkan poin-poin tujuan yang ingin dicapai.');
          input_field('Target Peserta', 'Target_Peserta', 'text', 'Contoh: Mahasiswa Fakultas Teknik Informasi, Umum');
          input_field('Jumlah Anggota Panitia Keseluruhan', 'Jumlah_Anggota_Panitia_Total', 'number', 'Contoh: 25');
          input_field('Durasi Acara', 'Durasi_Acara', 'text', 'Contoh: 3 Jam / 1 Hari Penuh');
          input_field('Waktu Acara Mulai', 'Waktu_Acara_Mulai', 'time');
          input_field('Waktu Acara Selesai', 'Waktu_Acara_Selesai', 'time');

          echo "<h5 class='form-subtitle'>Lampiran I - Susunan Panitia Inti</h5>";
          input_field('Nama Penanggung Jawab (Lampiran)', 'Lamp_Nama_Penanggung_Jawab', 'text', '', false, '', '', true);
          input_field('Nama Ketua Pelaksana (Lampiran)', 'Lamp_Nama_Ketua_Pelaksana', 'text', '', false, '', '', true);
          input_field('Nama Koordinator Divisi Sekretaris', 'Nama_Koordinator_Sekretaris', 'select');

          echo "<div class='mb-3'><label class='form-label'>Anggota Divisi Sekretaris</label>";
          for ($i = 1; $i <= $jumlah_anggota_per_divisi; $i++) {
              input_field("Anggota Sekretaris {$i}", "Nama_Anggota_Sekretaris_{$i}", 'select', '', $i === 1);
          }
          echo "</div>";

          input_field('Nama Koordinator Divisi Acara', 'Nama_Koordinator_Acara', 'select');
          echo "<div class='mb-3'><label class='form-label'>Anggota Divisi Acara</label>";
          for ($i = 1; $i <= $jumlah_anggota_per_divisi; $i++) {
              input_field("Anggota Acara {$i}", "Nama_Anggota_Acara_{$i}", 'select', '', $i === 1);
          }
          echo "</div>";

          input_field('Nama Koordinator Divisi Humas', 'Nama_Koordinator_Humas', 'select');
          echo "<div class='mb-3'><label class='form-label'>Anggota Divisi Humas</label>";
          for ($i = 1; $i <= $jumlah_anggota_per_divisi; $i++) {
               input_field("Anggota Humas {$i}", "Nama_Anggota_Humas_{$i}", 'select', '', $i === 1);
          }
          echo "</div>";

          input_field('Nama Koordinator Divisi PDD (Publikasi, Dekorasi, Dokumentasi)', 'Nama_Koordinator_PDD', 'select');
          echo "<div class='mb-3'><label class='form-label'>Anggota Divisi PDD</label>";
          for ($i = 1; $i <= $jumlah_anggota_per_divisi; $i++) {
              input_field("Anggota PDD {$i}", "Nama_Anggota_PDD_{$i}", 'select', '', $i === 1);
          }
          echo "</div>";
          ?>
          <div class="d-grid gap-2">
            <button type="submit" class="btn btn-submit-custom mt-3">Generate dan Kirim Proposal</button>
          </div>
        </form>
        <!-- Custom Success Modal -->
    <div id="successModal" class="modal-custom">
      <div class="modal-content-custom">
        <h5>✅ Proposal berhasil digenerate!</h5>
        <p>File Word sudah terunduh otomatis.</p>
        <button onclick="redirectDashboard()" class="btn btn-primary mt-3">Kembali ke Dashboard</button>
      </div>
    </div>

  </div>
</main>

  <!-- Frame tersembunyi untuk download file -->
  <iframe name="hidden_iframe" style="display:none;"></iframe>

  <script>
  // Cek apakah URL ada ?success=1
  function getQueryParam(param) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
  }

  document.addEventListener('DOMContentLoaded', () => {
    // Sinkronisasi nama Ketua Pelaksana (form ke lampiran readonly)
    const ketuaForm = document.getElementById('Nama_Ketua_Pelaksana_Form');
    const ketuaLampiran = document.getElementById('Lamp_Nama_Ketua_Pelaksana');

    ketuaForm.addEventListener('change', () => {
      ketuaLampiran.value = ketuaForm.value;
      ketuaLampiran.dispatchEvent(new Event('change')); // Update dropdown terkait
    });

    // Sinkronisasi nama Penanggung Jawab (form ke lampiran readonly)
    const pjForm = document.getElementById('Nama_PJ_Form');
    const pjLampiran = document.getElementById('Lamp_Nama_Penanggung_Jawab');

    pjForm.addEventListener('change', () => {
      pjLampiran.value = pjForm.value;
      pjLampiran.dispatchEvent(new Event('change'));
    });
  });

  // Fungsi validasi NIM 10 digit angka sebelum submit
  function validateFormClientSide() {
      const nimKetua = document.getElementById('NIM_Ketua_Pelaksana_Form').value;
      const nimPJ = document.getElementById('NIM_PJ_Form').value;
      const nimPattern = /^\d{10}$/;

      if (!nimPattern.test(nimKetua)) {
          alert('NIM Ketua Pelaksana harus terdiri dari 10 digit angka.');
          return false;
      }
      if (!nimPattern.test(nimPJ)) {
          alert('NIM Penanggung Jawab harus terdiri dari 10 digit angka.');
          return false;
      }
      return true;
  }

  document.addEventListener('DOMContentLoaded', () => {
    // Referensi semua dropdown nama anggota panitia
    const selects = document.querySelectorAll('select.nama-anggota');

    // Simpan opsi asli tiap dropdown supaya bisa reset saat update
    const originalOptions = {};
    selects.forEach(select => {
      originalOptions[select.id] = Array.from(select.options).map(opt => ({
        value: opt.value,
        text: opt.text,
      }));
    });

    // Update dropdown agar nama yang sudah dipilih di dropdown lain tidak muncul lagi
    function updateDropdowns() {
      const selectedValues = Array.from(selects)
        .map(s => s.value)
        .filter(v => v !== '');

      selects.forEach(select => {
        const currentValue = select.value;
        const opts = originalOptions[select.id];

        select.innerHTML = '';

        // Jika mau placeholder "-- Pilih Nama --" di dropdown, aktifkan baris berikut:
        select.appendChild(new Option(''));

        opts.forEach(opt => {
          if (opt.value === '' || opt.value === currentValue || !selectedValues.includes(opt.value)) {
            select.appendChild(new Option(opt.text, opt.value, false, opt.value === currentValue));
          }
        });
      });
    }

    // Sinkronisasi nama ketua dan penanggung jawab ke lampiran
    const ketuaForm = document.getElementById('Nama_Ketua_Pelaksana_Form');
    const ketuaLampiran = document.getElementById('Lamp_Nama_Ketua_Pelaksana');
    const pjForm = document.getElementById('Nama_PJ_Form');
    const pjLampiran = document.getElementById('Lamp_Nama_Penanggung_Jawab');

    function syncToLampiran() {
      ketuaLampiran.value = ketuaForm.value;
      pjLampiran.value = pjForm.value;
    }

    ketuaForm.addEventListener('change', () => {
      syncToLampiran();
      updateDropdowns();
    });
    pjForm.addEventListener('change', () => {
      syncToLampiran();
      updateDropdowns();
    });

    selects.forEach(select => {
      select.addEventListener('change', updateDropdowns);
    });

    // Inisialisasi saat halaman load
    syncToLampiran();
    updateDropdowns();
  });
  </script>

  <script>
function onSubmitForm() {
  setTimeout(function () {
    showSuccessModal();
  }, 2000); // Delay agar file sempat didownload
}

function showSuccessModal() {
  document.getElementById('successModal').style.display = 'block';
}

function redirectDashboard() {
  window.location.href = "../src/dashboard_user.php";
}
</script>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
