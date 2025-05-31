<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Form Proposal Lengkap</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
</head>
<body>
  <div class="container mt-4 mb-4">
    <h2 class="mb-4">Form Input Proposal Lengkap</h2>
    <form method="POST" action="generate_proposal_webinar.php" enctype="multipart/form-data">
      <div class="mb-3">
        <label for="Nama_Kegiatan" class="form-label">Nama Kegiatan</label>
        <input type="text" class="form-control" id="Nama_Kegiatan" name="Nama_Kegiatan" required />
      </div>

      <div class="mb-3">
        <label for="Judul_Kegiatan" class="form-label">Judul Kegiatan</label>
        <input type="text" class="form-control" id="Judul_Kegiatan" name="Judul_Kegiatan" required />
      </div>

      <div class="mb-3">
        <label for="Periode Aslab" class="form-label">Periode Aslab</label>
        <input type="text" class="form-control" id="Periode_Aslab" name="Periode_Aslab" required />
      </div>

      <div class="mb-3">
        <label for="Tahun" class="form-label">Tahun</label>
        <input type="text" class="form-control" id="Tahun" name="Tahun" required />
      </div>

      <div class="mb-3">
        <label for="Waktu_Pelaksanaan" class="form-label">Waktu Pelaksanaan</label>
        <input type="text" class="form-control" id="Waktu_Pelaksanaan" name="Waktu_Pelaksanaan" required />
      </div>

      <div class="mb-3">
        <label for="Tempat_Kegiatan" class="form-label">Tempat</label>
        <input type="text" class="form-control" id="Tempat_Kegiatan" name="Tempat_Kegiatan" required />
      </div>

      <div class="mb-3">
        <label for="Total_Biaya" class="form-label">Total Biaya</label>
        <input type="text" class="form-control" id="Total_Biaya" name="Total_Biaya" required />
      </div>

      <div class="mb-3">
        <label for="TTD_Ketua_Pelaksana" class="form-label">Tanda Tangan Ketua Pelaksana</label><br>
        <input type="file" class="" id="TTD_Ketua_Pelaksana" name="TTD_Ketua_Pelaksana" required>
      </div>

      <div class="mb-3">
        <label for="TTD_Penanggung_Jawab" class="form-label">Tanda Tangan Penanggung Jawab</label><br>
        <input type="file" class="" id="TTD_Penanggung_Jawab" name="TTD_Penanggung_Jawab" required>
      </div>

      <div class="mb-3">
        <label for="Tanggal_Penulisan_Proposal" class="form-label">Tanggal (Surat Pengesahan)</label>
        <input type="date" class="form-control" id="Tanggal_Penulisan_Proposal" name="Tanggal_Penulisan_Proposal" required />
      </div>

      <div class="mb-3">
        <label for="Nama_Ketua_Pelaksana" class="form-label">Nama Ketua Pelaksana</label>
        <input type="text" class="form-control" id="Nama_Ketua_Pelaksana" name="Nama_Ketua_Pelaksana" required />
      </div>

      <div class="mb-3">
        <label for="Nama_Penanggung_Jawab" class="form-label">Nama Penanggung Jawab</label>
        <input type="text" class="form-control" id="Nama_Penanggung_Jawab" name="Nama_Penanggung_Jawab" required />
      </div>

      <div class="mb-3">
        <label for="NIM_Ketua_Pelaksana" class="form-label">NIM Ketua Pelaksana</label>
        <input type="text" class="form-control" id="NIM_Ketua_Pelaksana" name="NIM_Ketua_Pelaksana" required />
      </div>

      <div class="mb-3">
        <label for="NIM_Penanggung_Jawab" class="form-label">NIM Penanggung Jawab</label>
        <input type="text" class="form-control" id="NIM_Penanggung_Jawab" name="NIM_Penanggung_Jawab" required />
      </div>

      <div class="mb-3">
        <label for="Tempat_tanggal_surat_ditulis" class="form-label">Tempat dan tanggal surat ditulis</label>
        <input type="text" class="form-control" id="Tempat_tanggal_surat_ditulis" name="Tempat_tanggal_surat_ditulis" required />
      </div>

      <div class="mb-3">
        <label for="Penulis" class="form-label">Penulis</label>
        <input type="text" class="form-control" id="Penulis" name="Penulis" required />
      </div>

      <div class="mb-3">
        <label for="Tujuan_Kegiatan" class="form-label">Tujuan Kegiatan</label>
        <textarea class="form-control" id="Tujuan_Kegiatan" name="Tujuan_Kegiatan" required></textarea>
      </div>

      <div class="mb-3">
        <label for="Latar_Belakang" class="form-label">Latar Belakang</label>
        <textarea class="form-control" id="Latar_Belakang" name="Latar_Belakang" required></textarea>
      </div>

      <div class="mb-3">
        <label for="Target_Peserta" class="form-label">Target Peserta</label>
        <input type="text" class="form-control" id="Target_Peserta" name="Target_Peserta" required />
      </div>


      <div class="mb-3">
        <label for="Tanggal_Kegiatan" class="form-label">Tanggal Kegiatan</label>
        <input type="date" class="form-control" id="Tanggal_Kegiatan" name="Tanggal_Kegiatan" required />
      </div>

      <div class="mb-3">
        <label for="Waktu_Kegiatan" class="form-label">Waktu Kegiatan</label>
        <input type="text" class="form-control" id="Waktu_Kegiatan" name="Waktu_Kegiatan" required />
      </div>

      <div class="mb-3">
        <label for="Jumlah_Anggota" class="form-label">Jumlah Anggota Panitia</label>
        <input type="text" class="form-control" id="Jumlah_Anggota" name="Jumlah_Anggota" required />
      </div>

      <div class="mb-3">
        <label for="Durasi_Acara" class="form-label">Durasi Acara</label>
        <input type="text" class="form-control" id="Durasi_Acara" name="Durasi_Acara" required />
      </div>

      <div class="mb-3">
        <label for="Waktu_Acara_Bermulai" class="form-label">Waktu Acara Bermulai</label>
        <input type="time" class="form-control" id="Waktu_Acara_Bermulai" name="Waktu_Acara_Bermulai" required />
      </div>

      <div class="mb-3">
        <label for="Waktu_Acara_Berakhir" class="form-label">Waktu Acara Berakhir</label>
        <input type="time" class="form-control" id="Waktu_Acara_Berakhir" name="Waktu_Acara_Berakhir" required />
      </div>

      <h3>Lampiran I - Susunan Panitia</h3>

      <div class="mb-3">
        <label for="Nama_Penanggung_Jawab" class="form-label">Nama Penanggung Jawab</label>
        <input type="text" class="form-control" id="Nama_Penanggung_Jawab" name="Nama_Penanggung_Jawab" required />
      </div>

      <div class="mb-3">
        <label for="Nama_Ketua_Pelaksana" class="form-label">Nama Ketua Pelaksana</label>
        <input type="text" class="form-control" id="Nama_Ketua_Pelaksana" name="Nama_Ketua_Pelaksana" required />
      </div>

      <div class="mb-3">
        <label for="Nama_Koordinator_Sekretaris" class="form-label">Nama Koordinator Sekretaris</label>
        <input type="text" class="form-control" id="Nama_Koordinator_Sekretaris" name="Nama_Koordinator_Sekretaris" required />
      </div>

      <div class="mb-3">
        <label for="Nama_Anggota_Sekretaris_1" class="form-label">Nama Anggota Sekretaris 1</label>
        <input type="text" class="form-control" id="Nama_Anggota_Sekretaris_1" name="Nama_Anggota_Sekretaris_1" required />
      </div>

      <div class="mb-3">
        <label for="Nama_Anggota_Sekretaris_2" class="form-label">Nama Anggota Sekretaris 2</label>
        <input type="text" class="form-control" id="Nama_Anggota_Sekretaris_2" name="Nama_Anggota_Sekretaris_2" required />
      </div>

      <div class="mb-3">
        <label for="Nama_Anggota_Sekretaris_3" class="form-label">Nama Anggota Sekretaris 3</label>
        <input type="text" class="form-control" id="Nama_Anggota_Sekretaris_3" name="Nama_Anggota_Sekretaris_3" required />
      </div>

      <div class="mb-3">
        <label for="Nama_Koordinator_Acara" class="form-label">Nama Koordinator Acara</label>
        <input type="text" class="form-control" id="Nama_Koordinator_Acara" name="Nama_Koordinator_Acara" required />
      </div>

      <div class="mb-3">
        <label for="Nama_Anggota_Acara_1" class="form-label">Nama Anggota Acara 1</label>
        <input type="text" class="form-control" id="Nama_Anggota_Acara_1" name="Nama_Anggota_Acara_1" required />
      </div>

      <div class="mb-3">
        <label for="Nama_Anggota_Acara_2" class="form-label">Nama Anggota Acara 2</label>
        <input type="text" class="form-control" id="Nama_Anggota_Acara_2" name="Nama_Anggota_Acara_2" required />
      </div>

      <div class="mb-3">
        <label for="Nama_Anggota_Acara_3" class="form-label">Nama Anggota Acara 3</label>
        <input type="text" class="form-control" id="Nama_Anggota_Acara_3" name="Nama_Anggota_Acara_3" required />
      </div>

      <div class="mb-3">
        <label for="Nama_Anggota_Acara_4" class="form-label">Nama Anggota Acara 4</label>
        <input type="text" class="form-control" id="Nama_Anggota_Acara_4" name="Nama_Anggota_Acara_4" required />
      </div>

      <div class="mb-3">
        <label for="Nama_Anggota_Acara_5" class="form-label">Nama Anggota Acara 5</label>
        <input type="text" class="form-control" id="Nama_Anggota_Acara_5" name="Nama_Anggota_Acara_5" required />
      </div>

      <div class="mb-3">
        <label for="Nama_Anggota_Acara_6" class="form-label">Nama Anggota Acara 6</label>
        <input type="text" class="form-control" id="Nama_Anggota_Acara_6" name="Nama_Anggota_Acara_6" required />
      </div>

      <div class="mb-3">
        <label for="Nama_Koordinator_Humas" class="form-label">Nama Koordinator Humas</label>
        <input type="text" class="form-control" id="Nama_Koordinator_Humas" name="Nama_Koordinator_Humas" required />
      </div>

      <div class="mb-3">
        <label for="Nama_Anggota_Humas_1" class="form-label">Nama Anggota Humas 1</label>
        <input type="text" class="form-control" id="Nama_Anggota_Humas_1" name="Nama_Anggota_Humas_1" required />
      </div>

      <div class="mb-3">
        <label for="Nama_Anggota_Humas_2" class="form-label">Nama Anggota Humas 2</label>
        <input type="text" class="form-control" id="Nama_Anggota_Humas_2" name="Nama_Anggota_Humas_2" required />
      </div>

      <div class="mb-3">
        <label for="Nama_Anggota_Humas_3" class="form-label">Nama Anggota Humas 3</label>
        <input type="text" class="form-control" id="Nama_Anggota_Humas_3" name="Nama_Anggota_Humas_3" required />
      </div>

      <div class="mb-3">
        <label for="Nama_Koordinator_PDD" class="form-label">Nama Koordinator PDD</label>
        <input type="text" class="form-control" id="Nama_Koordinator_PDD" name="Nama_Koordinator_PDD" required />
      </div>

      <div class="mb-3">
        <label for="Nama_Anggota_PDD_1" class="form-label">Nama Anggota PDD 1</label>
        <input type="text" class="form-control" id="Nama_Anggota_PDD_1" name="Nama_Anggota_PDD_1" required />
      </div>

      <div class="mb-3">
        <label for="Nama_Anggota_PDD_2" class="form-label">Nama Anggota PDD 2</label>
        <input type="text" class="form-control" id="Nama_Anggota_PDD_2" name="Nama_Anggota_PDD_2" required />
      </div>

      <div class="mb-3">
        <label for="Nama_Anggota_PDD_3" class="form-label">Nama Anggota PDD 3</label>
        <input type="text" class="form-control" id="Nama_Anggota_PDD_3" name="Nama_Anggota_PDD_3" required />
      </div>

      <div class="mb-3">
        <label for="Nama_Anggota_PDD_4" class="form-label">Nama Anggota PDD 4</label>
        <input type="text" class="form-control" id="Nama_Anggota_PDD_4" name="Nama_Anggota_PDD_4" required />
      </div>

      <div class="mb-3">
        <label for="Nama_Anggota_PDD_5" class="form-label">Nama Anggota PDD 5</label>
        <input type="text" class="form-control" id="Nama_Anggota_PDD_5" name="Nama_Anggota_PDD_5" required />
      </div>

      <div class="mb-3">
        <label for="Nama_Anggota_PDD_6" class="form-label">Nama Anggota PDD 6</label>
        <input type="text" class="form-control" id="Nama_Anggota_PDD_6" name="Nama_Anggota_PDD_6" required />
      </div>

      <div class="mb-3">
        <label for="Nama_Anggota_PDD_7" class="form-label">Nama Anggota PDD 7</label>
        <input type="text" class="form-control" id="Nama_Anggota_PDD_7" name="Nama_Anggota_PDD_7" required />
      </div>
      <button type="submit" class="btn btn-primary mt-3">Generate Proposal</button>
    </div>
  </form>

  <button><a href="dashboard_user.php">Kembali ke Dashboard</a></button>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-pP8IlA0UHSzkkm4P7mRMqX1l5RQXRyqNHr8zF4X34kCYjIOy1LlF7EY6NHOKy7+N" crossorigin="anonymous"></script>
</body>
</html>