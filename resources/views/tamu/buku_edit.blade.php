<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Tamu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">

<div class="min-h-screen p-6 flex items-center justify-center">
    <div class="max-w-2xl w-full">
        
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Edit Data Tamu</h1>
            <p class="text-gray-600 mt-2">ID: <span class="font-bold">#{{ $tamu->id }}</span></p>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-lg shadow-md p-8">
            <form id="editForm">
                @csrf

                <!-- Jenis Tamu -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Tamu</label>
                    <select name="jenis_tamu" id="jenis_tamu" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required onchange="toggleFields()">
                        <option value="guru" {{ $tamu->jenis_tamu === 'guru' ? 'selected' : '' }}>Guru</option>
                        <option value="siswa" {{ $tamu->jenis_tamu === 'siswa' ? 'selected' : '' }}>Murid</option>
                    </select>
                </div>

                <!-- Nama -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                    <input type="text" name="nama" value="{{ $tamu->nama }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>

                <!-- Guru Fields -->
                <div id="field_guru" class="{{ $tamu->jenis_tamu !== 'guru' ? 'hidden' : '' }} mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mata Pelajaran</label>
                    <input type="text" name="mapel" value="{{ $tamu->mapel }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Siswa Fields -->
                <div id="field_siswa" class="{{ $tamu->jenis_tamu !== 'siswa' ? 'hidden' : '' }} mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kelas</label>
                            <input type="text" name="kelas" value="{{ $tamu->kelas }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jurusan</label>
                            <input type="text" name="jurusan" value="{{ $tamu->jurusan }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex gap-4">
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                    <a href="{{ route('tamu.buku.index') }}" class="flex-1 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition font-medium text-center flex items-center justify-center gap-2">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
            </form>
        </div>

    </div>
</div>

<script>
    function toggleFields() {
        const jenis = document.getElementById('jenis_tamu').value;
        document.getElementById('field_guru').classList.toggle('hidden', jenis !== 'guru');
        document.getElementById('field_siswa').classList.toggle('hidden', jenis !== 'siswa');
    }

    document.getElementById('editForm').addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(e.target);
        const data = {
            nama: formData.get('nama'),
            jenis_tamu: formData.get('jenis_tamu'),
            mapel: formData.get('mapel'),
            kelas: formData.get('kelas'),
            jurusan: formData.get('jurusan'),
            _token: document.querySelector('meta[name="csrf-token"]')?.content || ''
        };

        try {
            const response = await fetch('{{ route("tamu.buku.update", $tamu->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': data._token
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if(response.ok) {
                alert('✓ Data berhasil diperbarui');
                window.location.href = '{{ route("tamu.buku.show", $tamu->id) }}';
            } else {
                alert('❌ ' + result.message);
            }
        } catch (error) {
            alert('❌ Error: ' + error.message);
        }
    });
</script>

</body>
</html>