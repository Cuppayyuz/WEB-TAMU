<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Tamu - List</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">

<div class="min-h-screen p-6">
    <div class="max-w-7xl mx-auto">
        
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">📖 Buku Tamu</h1>
                <p class="text-gray-600 mt-2">Total: <span class="font-bold text-blue-600">{{ $tamus->total() }}</span> Tamu</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('tamu.buku.export.excel') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                    <i class="fas fa-file-excel"></i> Excel
                </a>
                <a href="{{ route('tamu.buku.export.pdf') }}" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition flex items-center gap-2">
                    <i class="fas fa-file-pdf"></i> PDF
                </a>
            </div>
        </div>

        <!-- Filter & Search Card -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <form method="GET" action="{{ route('tamu.buku.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                
                <!-- Search by Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cari Nama</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama tamu..." 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Filter Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="semua">Semua</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>

                <!-- Filter Jenis Tamu -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis</label>
                    <select name="jenis_tamu" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="semua">Semua</option>
                        <option value="guru" {{ request('jenis_tamu') === 'guru' ? 'selected' : '' }}>Guru</option>
                        <option value="siswa" {{ request('jenis_tamu') === 'siswa' ? 'selected' : '' }}>Murid</option>
                    </select>
                </div>

                <!-- Date From -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Dari Tanggal</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Date To -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sampai Tanggal</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Submit Button -->
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                        <i class="fas fa-search"></i> Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-200 border-b-2 border-gray-300">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">No</th>
                        <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">Nama</th>
                        <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">Jenis</th>
                        <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">Detail</th>
                        <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">Tanggal</th>
                        <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">Jam</th>
                        <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">Status</th>
                        <th class="px-6 py-4 text-center text-sm font-bold text-gray-700">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tamus as $tamu)
                    <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm font-bold text-gray-800">{{ $tamus->firstItem() + $loop->index }}</td>
                        <td class="px-6 py-4 text-sm text-gray-800 font-medium">{{ $tamu->nama }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold">
                                {{ ucfirst($tamu->jenis_tamu) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            @if($tamu->jenis_tamu === 'guru')
                                <span class="text-xs">{{ $tamu->mapel ?? '-' }}</span>
                            @else
                                <span class="text-xs">{{ $tamu->kelas ?? '-' }} - {{ $tamu->jurusan ?? '-' }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $tamu->created_at->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $tamu->created_at->format('H:i:s') }}</td>
                        <td class="px-6 py-4 text-sm">
                            @if($tamu->status === 'draft')
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold">Draft</span>
                            @else
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">Selesai</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex gap-2 justify-center">
                                <a href="{{ route('tamu.buku.show', $tamu->id) }}" class="px-3 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600 transition">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('tamu.buku.edit', $tamu->id) }}" class="px-3 py-1 bg-yellow-500 text-white text-xs rounded hover:bg-yellow-600 transition">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="deleteTamu({{ $tamu->id }})" class="px-3 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600 transition">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2"></i>
                            <p>Tidak ada data tamu</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6 flex justify-center">
            {{ $tamus->links() }}
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
                    location.reload();
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