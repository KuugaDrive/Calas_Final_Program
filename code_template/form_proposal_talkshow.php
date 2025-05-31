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
    "Rayvano Putra Pratama", "Zidane Putra Waluyo", "Ahvaz Haidar Gaza Wahyudi", "Aktar Faizil", "Astri Ananta Kartini",
    "Davi Rizky Madani", "Inaya Zehan Kalyzta", "Muhammad Rafif Rabbani", "Muhammad Razin Haidar Karim",
    "Muhammad Rfiky Ramadhani", "Naufa Aulia Sabila Azzahra", "Rizky Saputra", "Shandyka Azmi", "Syukranda"
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
    } elseif ($type === 'file') {
        echo "<input type='file' class='form-control' id='{$name}' name='{$name}' accept='.png,.jpg,.jpeg' {$req_attr}>";
    } else {
        if (in_array($name, ['NIM_Ketua_Pelaksana_Form', 'NIM_Penanggung_Jawab_Form', 'NIM_Sekretaris_Pelaksana_Form']) && $type === 'text') {
            echo "<input type='text' class='form-control' id='{$name}' name='{$name}' placeholder='{$placeholder}' maxlength='10' pattern='\\d{10}' oninput='this.value=this.value.replace(/[^0-9]/g,\"\")' title='Harus 10 digit angka' {$req_attr} />";
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
  <title>Form Input LPJ Workshop</title>
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
  </style>
</head>
<body>
  <div class="wrapper">
    <nav class="sidebar">
      <img src="../src/img/logo_lab.png" alt="Logo Lab ICT" class="logo" />
      <a href="../src/dashboard_user.php">Dashboard</a>
      <a href="../src/proposal.php">Proposal</a>
      <a href="../src/lpj.php" class="active">LPJ</a>
      <a href="../src/sertifikat.php">Sertifikat</a>
      <a href="../src/banner.php">Banner</a>
      <a href="../src/dokumentasi.php">Dokumentasi</a>
    </nav>

    <main class="main-content">
      <div class="content-wrapper">
        <h2 class="form-title">Form Input LPJ Workshop</h2>

        <form method="POST" action="generate_lpj_workshop.php" enctype="multipart/form-data" target="hidden_iframe" onsubmit="return validateFormClientSide() && onSubmitForm()">

          <?php
          // Identitas Kegiatan
          input_field('Nama Kegiatan', 'Nama_Kegiatan', 'text', 'Contoh: WorthIT#10 (Workshop Tahunan Informasi dan Teknologi) LAB ICT');
          input_field('Judul Kegiatan', 'Judul_Kegiatan', 'text', 'Contoh: WorthIT#10 : Boost Your IT Skills ');
          input_field('Periode Asisten Lab', 'Periode_Aslab', 'text', 'Contoh: 2024/2025');
          input_field('Tahun Kegiatan', 'Tahun_Kegiatan', 'number', 'Contoh: 2024');
          input_field('Hari/Tanggal Kegiatan', 'Hari_Tanggal_Kegiatan', 'text', 'Contoh: Sabtu, 20 Juli 2024');
          input_field('Tempat Kegiatan', 'Tempat_Kegiatan', 'text', 'Contoh: Lab ICT');
          input_field('Anggaran Dana (Rp)', 'Anggaran_Dana', 'text', 'Contoh: Rp.1.500.000,00');

          // Detail Pelaksanaan Acara
          input_field('Tanggal Pengesahan', 'Tanggal_Pengesahan', 'date', '', true);
          input_field('Waktu Acara Bermulai', 'Waktu_Acara_Bermulai', 'time', 'Contoh: 09:00');
          input_field('Waktu Acara Berakhir', 'Waktu_Acara_Berakhir', 'time', 'Contoh: 12:00');
          input_field('Tempat Acara', 'Tempat_Acara', 'text', 'Contoh: Ruang Lab 3');

          // Penanggung Jawab dan Kepanitiaan
          input_field('Nama Penanggung Jawab (Form)', 'Nama_Penanggung_Jawab_Form', 'select');
          input_field('NIM Penanggung Jawab (Form)', 'NIM_Penanggung_Jawab_Form', 'text', 'Contoh: 2311500546', true, '', 'Harus 10 digit angka');
          input_field('Nama Ketua Pelaksana (Form)', 'Nama_Ketua_Pelaksana_Form', 'select');
          input_field('NIM Ketua Pelaksana (Form)', 'NIM_Ketua_Pelaksana_Form', 'text', 'Contoh: 2412501914', true, '', 'Harus 10 digit angka');
          input_field('Nama Sekretaris Pelaksana (Form)', 'Nama_Sekretaris_Pelaksana_Form', 'select');
          input_field('Nama Bendahara (Form)', 'Nama_Bendahara_Form', 'select');
          input_field('NIM Sekretaris Pelaksana (Form)', 'NIM_Sekretaris_Pelaksana_Form', 'text', 'Contoh: 2411501234', true, '', 'Harus 10 digit angka');

          // Upload TTD
          input_field('Unggah TTD Ketua Pelaksana', 'TTD_Ketua_Pelaksana', 'file', '', true, '', 'Format: .png, .jpg, .jpeg. Maks: 1MB');
          input_field('Unggah TTD Sekretaris Pelaksana', 'TTD_Sekretaris_Pelaksana', 'file', '', true, '', 'Format: .png, .jpg, .jpeg. Maks: 1MB');
          input_field('Unggah TTD Penanggung Jawab', 'TTD_Penanggung_Jawab', 'file', '', true, '', 'Format: .png, .jpg, .jpeg. Maks: 1MB');

          // Hasil yang Dicapai
          textarea_field('Hasil yang Dicapai', 'Hasil_Yang_Dicapai', 5, 'Jelaskan hasil yang dicapai selama kegiatan workshop', true);


          //MC dan Moderator
          input_field('Nama MC', 'Nama_MC', 'select');
          input_field('Nama Moderator', 'Nama_Moderator', 'text');


          // Panitia per sie
          echo "<h5>Sie Acara</h5>";
          for ($i = 1; $i <= $jumlah_anggota_per_divisi; $i++) {
              input_field("Anggota Acara {$i}", "Nama_Anggota_Acara_{$i}", 'select', '', $i === 1);
          }

          echo "<h5>Sie Humas</h5>";
          for ($i = 1; $i <= $jumlah_anggota_per_divisi; $i++) {
              input_field("Anggota Humas {$i}", "Nama_Anggota_Humas_{$i}", 'select', '', $i === 1);
          }

          echo "<h5>Sie Dokumentasi & Perlengkapan</h5>";
          for ($i = 1; $i <= $jumlah_anggota_per_divisi; $i++) {
              input_field("Anggota Dokumentasi {$i}", "Nama_Anggota_PDD_{$i}", 'select', '', $i === 1);
          }

          echo "<h5>Sie Konsumsi</h5>";
          for ($i = 1; $i <= $jumlah_anggota_per_divisi; $i++) {
              input_field("Anggota Konsumsi {$i}", "Nama_Anggota_Konsumsi_{$i}", 'select', '', $i === 1);
          }

          echo "<h5>Sie Registrasi</h5>";
          for ($i = 1; $i <= $jumlah_anggota_per_divisi; $i++) {
              input_field("Anggota Registrasi {$i}", "Nama_Anggota_Registrasi_{$i}", 'select', '', $i === 1);
          }

          // Materi dan Instruktur
          for ($m = 1; $m <= 3; $m++) {
              input_field("Materi {$m}", "Materi_{$m}", 'text', "Judul materi {$m}", true);
              input_field("Nama Instruktur Materi {$m}", "Nama_Instruktur_{$m}", 'select', '', true);
              input_field("Nama Asisten Instruktur Materi {$m}", "Nama_Asisten_Instruktur_{$m}", 'select', '', false);
          }
          ?>

          <?php
          // Tambahkan upload file untuk lampiran gambar
          $lampiran_images = [
          'Lampiran_Sertifikat_Materi_1', 'Lampiran_Sertifikat_Materi_2', 'Lampiran_Sertifikat_Materi_3',
          'Lampiran_Dokumentasi'
          ];

          echo "<h5>Lampiran Gambar</h5>";
          foreach ($lampiran_images as $lampiran) {
              input_field("Upload $lampiran", $lampiran, 'file', '', false);
          }
          ?>

          <div class="d-grid gap-2">
            <button type="submit" class="btn btn-submit-custom mt-3">Generate dan Kirim LPJ Workshop</button>
          </div>
        </form>
      </div>
    </main>
  </div>

  <!-- Frame tersembunyi untuk download file -->
  <iframe name="hidden_iframe" style="display:none;"></iframe>

  <script>
  function validateFormClientSide() {
      const nimKetua = document.getElementById('NIM_Ketua_Pelaksana_Form').value;
      const nimPJ = document.getElementById('NIM_Penanggung_Jawab_Form').value;
      const nimSekretaris = document.getElementById('NIM_Sekretaris_Pelaksana_Form').value;
      const nimPattern = /^\d{10}$/;

      if (!nimPattern.test(nimKetua)) {
          alert('NIM Ketua Pelaksana harus terdiri dari 10 digit angka.');
          return false;
      }
      if (!nimPattern.test(nimPJ)) {
          alert('NIM Penanggung Jawab harus terdiri dari 10 digit angka.');
          return false;
      }
      if (!nimPattern.test(nimSekretaris)) {
          alert('NIM Sekretaris Pelaksana harus terdiri dari 10 digit angka.');
          return false;
      }
      return true;
  }

  document.addEventListener('DOMContentLoaded', () => {
    const selects = document.querySelectorAll('select.nama-anggota');

    const originalOptions = {};
    selects.forEach(select => {
      originalOptions[select.id] = Array.from(select.options).map(opt => ({
        value: opt.value,
        text: opt.text,
      }));
    });

    function updateDropdowns() {
      const selectedValues = Array.from(selects)
        .map(s => s.value)
        .filter(v => v !== '');

      selects.forEach(select => {
        const currentValue = select.value;
        const opts = originalOptions[select.id];

        select.innerHTML = '';
        select.appendChild(new Option('-- Pilih Nama --', ''));

        opts.forEach(opt => {
          if (opt.value === '' || opt.value === currentValue || !selectedValues.includes(opt.value)) {
            select.appendChild(new Option(opt.text, opt.value, false, opt.value === currentValue));
          }
        });
      });
    }

    selects.forEach(select => {
      select.addEventListener('change', updateDropdowns);
    });

    updateDropdowns();
  });

  function onSubmitForm() {
    setTimeout(() => {
      alert('LPJ Workshop berhasil digenerate dan dikirim!');
    }, 1500);
  }
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
