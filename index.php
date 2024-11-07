<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Validasi & Tabel Data</title>

    <script src="https://cdn.jsdelivr.net/npm/just-validate@3.5.0/dist/just-validate.production.min.js"></script>

    <style>
        * {
            box-sizing:border-box;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 50px;
            background-color: #f0f0f0;
        }

        form {
            margin-bottom: 20px;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        input {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            margin-bottom: 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .is-invalid {
            border-color: red;
        }

        .error {
            color: red;
            font-size: 12px;
            margin-top: -10px;
            margin-bottom: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #bf00ff;
            color: white;
        }

        .loader {
            width: 48px;
            height: 48px;
            border: 5px solid #FFF;
            border-bottom-color: #FF3D00;
            border-radius: 50%;
            display: block;
            margin: 16px auto;
            box-sizing: border-box;
            animation: rotation 1s linear infinite;
        }

        @keyframes rotation {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .pagination button {
            padding: 8px 16px;
            margin: 0 5px;
            border: none;
            background-color: #bf00ff;
            color: white;
            cursor: pointer;
            border-radius: 4px;
        }

        .pagination button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
    </style>
</head>
<body>

    <form id="dataForm">
        <div>
            <input type="text" name="nik" class="nik" placeholder="Masukkan NIK">
            <p class="error nik--error"></p>
        </div>
        <div>
            <input type="text" name="name" class="name" placeholder="Masukkan Nama">
            <p class="error nama--error"></p>
        </div>
        <button type="submit">Simpan</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIK</th>
                <th>Nama</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>

    <div class="pagination">
        <button id="prevBtn" disabled>Previous</button>
        <button id="nextBtn">Next</button>
    </div>

    <span class="loader"></span>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        let currentPage = 1;
        const itemsPerPage = 3;

        function renderTable(data, page) {
            const table = document.querySelector('table tbody');
            table.innerHTML = '';
            const start = (page - 1) * itemsPerPage;
            const end = start + itemsPerPage;

            data.slice(start, end).forEach((student, index) => {
                const row = `
                    <tr>
                        <td>${start + index + 1}</td>
                        <td>${student.nik}</td>
                        <td>${student.nama}</td>
                    </tr>
                `;
                table.innerHTML += row;
            });

            document.getElementById('prevBtn').disabled = page === 1;
            document.getElementById('nextBtn').disabled = end >= data.length;
        }

        function fetchDataAndRender(page = 1) {
            axios.get('get-students.php').then(function(response) {
                const loader = document.querySelector('.loader');
                loader.style.display = 'none';
                renderTable(response.data, page);
            });
        }

        document.getElementById('prevBtn').addEventListener('click', function() {
            if (currentPage > 1) {
                currentPage--;
                fetchDataAndRender(currentPage);
            }
        });

        document.getElementById('nextBtn').addEventListener('click', function() {
            currentPage++;
            fetchDataAndRender(currentPage);
        });

        const validation = new JustValidate('#dataForm', {
            errorFieldCssClass: 'is-invalid',
            errorLabelStyle: { color: 'red', fontSize: '12px' },
        });

        validation
            .addField('.nik', [
                { rule: 'required', errorMessage: 'NIK tidak boleh kosong' },
                { rule: 'number', errorMessage: 'NIK harus berupa angka' },
            ])
            .addField('.name', [
                { rule: 'required', errorMessage: 'Nama tidak boleh kosong' },
                { rule: 'minLength', value: 3, errorMessage: 'Nama minimal 3 karakter' },
            ])
            .onSuccess((event) => {
                event.preventDefault();
                const nik = document.querySelector('.nik').value;
                const name = document.querySelector('.name').value;

                axios.post('save-students.php', { nik, name }).then(function(response) {
                    if (response.data.status === true) {
                        fetchDataAndRender(currentPage);
                        event.target.reset();
                    } else {
                        alert(response.data.error);
                    }
                });
            });

        fetchDataAndRender(currentPage);
    </script>

</body>
</html>
