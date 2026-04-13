<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Petugas</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg text-center">
        <h1 class="text-3xl font-bold mb-4">Meja Penerima Tamu</h1>
        <form action="{{ route('tamu.buatSesi') }}" method="POST">
            @csrf
            <button type="submit" class="bg-blue-600 text-white font-bold py-4 px-8 rounded-lg hover:bg-blue-700 text-xl shadow-lg">
                + Terima Tamu Baru
            </button>
        </form>
    </div>
</body>
</html>