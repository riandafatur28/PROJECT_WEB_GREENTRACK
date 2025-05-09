<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Detail Data Kayu</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }

        .popup-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }

        .popup-content {
            background: white;
            margin: 50px auto;
            padding: 20px;
            border-radius: 8px;
            width: 60%;
            max-width: 700px;
        }

        .popup-content img {
            width: 100%;
            border-radius: 8px;
        }

        .close-btn {
            float: right;
            cursor: pointer;
        }

        .detail-container {
            display: flex;
            gap: 20px;
        }

        .detail-form {
            flex: 1;
        }

        .detail-form input {
            width: 100%;
            margin-bottom: 10px;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        .btn {
            padding: 8px 16px;
            margin: 5px;
            border: none;
            border-radius: 4px;
        }

        .btn-save {
            background-color: #4CAF50;
            color: white;
        }

        .btn-delete {
            background-color: #ccc;
            color: black;
        }

        .btn-edit {
            background-color: #aaa;
            color: white;
        }
    </style>
</head>

<body>
    <button class="detail-btn" data-id="1" data-barcode="12345" data-jenis="Jati" data-volume="1.5 m³"
        data-diameter="30 cm" data-panjang="2 m" data-tanggungjawab="Pak Budi" data-tanggal="2025-05-08"
        data-kondisi="Baik">Lihat Selengkapnya</button>

    <div class="popup-overlay" id="popup">
        <div class="popup-content">
            <span class="close-btn" onclick="closePopup()">&times;</span>
            <h2>Detail Data Kayu</h2>
            <div class="detail-container">
                <img src="https://via.placeholder.com/300" alt="Kayu">
                <div class="detail-form">
                    <input id="barcode" placeholder="ID Barcode">
                    <input id="jenis" placeholder="Jenis Kayu">
                    <input id="volume" placeholder="Volume">
                    <input id="diameter" placeholder="Diameter">
                    <input id="panjang" placeholder="Panjang">
                    <input id="penanggung" placeholder="Penanggung Jawab">
                    <input id="tanggal" placeholder="Tanggal Kayu">
                    <input id="kondisi" placeholder="Kondisi Kayu">
                    <button class="btn btn-save">Simpan</button>
                    <button class="btn btn-delete">Hapus</button>
                    <button class="btn btn-edit">Edit</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openPopup(data) {
            document.getElementById('barcode').value = data.barcode;
            document.getElementById('jenis').value = data.jenis;
            document.getElementById('volume').value = data.volume;
            document.getElementById('diameter').value = data.diameter;
            document.getElementById('panjang').value = data.panjang;
            document.getElementById('penanggung').value = data.tanggungjawab;
            document.getElementById('tanggal').value = data.tanggal;
            document.getElementById('kondisi').value = data.kondisi;
            document.getElementById('popup').style.display = 'block';
        }

        function closePopup() {
            document.getElementById('popup').style.display = 'none';
        }

        document.querySelectorAll('.detail-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const data = {
                    barcode: this.getAttribute('data-barcode'),
                    jenis: this.getAttribute('data-jenis'),
                    volume: this.getAttribute('data-volume'),
                    diameter: this.getAttribute('data-diameter'),
                    panjang: this.getAttribute('data-panjang'),
                    tanggungjawab: this.getAttribute('data-tanggungjawab'),
                    tanggal: this.getAttribute('data-tanggal'),
                    kondisi: this.getAttribute('data-kondisi'),
                };
                openPopup(data);
            });
        });
    </script>
</body>

</html>
