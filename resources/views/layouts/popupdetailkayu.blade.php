<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Detail Data Kayu</title>
    <style>
        /* Styling Popup */
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
    <div class="popup-overlay" id="popup">
        <div class="popup-content">
            <span class="close-btn" onclick="closePopup()">&times;</span>
            <h2>Detail Data Kayu</h2>
            <div class="detail-container">
                <img src="https://via.placeholder.com/300" alt="Kayu">
                <div class="detail-form">
                    <input id="id" placeholder="ID Kayu" readonly>
                    <input id="nama" placeholder="Nama Kayu" readonly>
                    <input id="jenis" placeholder="Jenis Kayu" readonly>
                    <input id="usia" placeholder="Usia Kayu" readonly>
                    <input id="jumlah" placeholder="Jumlah" readonly>
                    <input id="lokasi" placeholder="Lokasi" readonly>
                    <input id="status" placeholder="Status Kayu" readonly>
                    <button class="btn btn-save">Simpan</button>
                    <button class="btn btn-delete">Hapus</button>
                    <button class="btn btn-edit">Edit</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Fungsi untuk membuka popup dan mengisi form dengan data yang disimpan di localStorage
        window.onload = function() {
            const kayuData = JSON.parse(localStorage.getItem('kayudetail'));
            if (kayuData) {
                document.getElementById('id').value = kayuData.id;
                document.getElementById('nama').value = kayuData.nama;
                document.getElementById('jenis').value = kayuData.jenis;
                document.getElementById('usia').value = kayuData.usia;
                document.getElementById('jumlah').value = kayuData.jumlah;
                document.getElementById('lokasi').value = kayuData.lokasi;
                document.getElementById('status').value = kayuData.status;
                document.getElementById('popup').style.display = 'block';
            }
        };

        // Fungsi untuk menutup popup
        function closePopup() {
            document.getElementById('popup').style.display = 'none';
        }
    </script>
</body>

</html>
