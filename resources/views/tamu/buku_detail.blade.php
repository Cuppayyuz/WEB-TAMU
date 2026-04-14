<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Tamu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">

<div class="min-h-screen p-6">
    <div class="max-w-4xl mx-auto">
        
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Detail Tamu</h1>
                <p class="text-gray-600 mt-2">ID: <span class="font-bold">#{{ $tamu->id }}</span></p>
            </div>
            <a href="{{ route('tamu.buku.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        <!-- Main Card -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- Info Column -->
            <div class="md:col-span-2">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                        <i class="fas fa-user-circle text-blue-600"></i>
                        {{ $tamu->nama }}
                    </h2>

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Tamu</label>
                            <p class="text-lg font-semibold text-gray-800">
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                                    {{ ucfirst($tamu->jenis_tamu) }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <p class="text-lg font-semibold">
                                @if($tamu->status === 'draft')
                                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm">Draft</span>
                                @else
                                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">Selesai</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-6 border-t border-gray-200 pt-4">
                        @if($tamu->jenis_tamu === 'guru')
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mata Pelajaran</label>
                                <p class="text-lg text-gray-800">{{ $tamu->mapel ?? '-' }}</p>
                            </div>
                        @else
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                                <p class="text-lg text-gray-800">{{ $tamu->kelas ?? '-' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jurusan</label>
                                <p class="text-lg text-gray-800">{{ $tamu->jurusan ?? '-' }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="border-t border-gray-200 pt-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Kunjungan</label>
                                <p class="text-lg text-gray-800 font-semibold">{{ $tamu->created_at->format('d/m/Y') }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jam Kunjungan</label>
                                <p class="text-lg text-gray-800 font-semibold">{{ $tamu->created_at->format('H:i:s') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="bg-white rounded-lg shadow-md p-6 mt-6 flex gap-3">
                    <a href="{{ route('tamu.buku.edit', $tamu->id) }}" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition flex items-center gap-2 flex-1">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <button onclick="deleteTamu({{ $tamu->id }})" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition flex items-center gap-2 flex-1">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </div>
            </div>

            <!-- Signature Column -->
            <div class="md:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        <i class="fas fa-pen-fancy text-purple-600"></i> Tanda Tangan
                    </h3>
                    
                    @if($tamu->tanda_tangan)
                        <div class="border-2 border-gray-300 rounded-lg p-2 bg-gray-50">
                            <img src="{{ Storage::url($tamu->tanda_tangan) }}" alt="Tanda Tangan" class="w-full h-auto rounded">
                        </div>
                        <p class="text-xs text-green-600 mt-2 text-center">✓ Tersimpan</p>
                    @else
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 bg-gray-50 text-center">
                            <i class="fas fa-inbox text-gray-400 text-3xl mb-2"></i>
                            <p class="text-gray-500 text-sm">Belum ada tanda tangan</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>

    </div>
</div>

<script>
    function deleteTamu(id) {
        if(confirm('Yakin ingin menghapus data tamu ini?')) {
            fetch(`/tamu/buku/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    alert('✓ Data berhasil dihapus');
                    window.location.href = '{{ route("tamu.buku.index") }}';
                } else {
                    alert('❌ ' + data.message);
                }
            })
            .catch(err => alert('❌ Error: ' + err.message));
        }
    }
</script>

</body>
</html>