<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Barang</title>
</head>
<body>
    <h1>Edit Barang</h1>

    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form
        action="{{ route('inventory.update', ['inventoryItem' => $inventoryItem->id]) }}"
        method="POST"
    >
        @csrf
        @method('PUT')

        <div>
            <label for="name">Nama Barang</label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name', $inventoryItem->name) }}"
                required
            >
        </div>

        <br>

        <div>
            <label for="category">Kategori</label>
            <input
                type="text"
                id="category"
                name="category"
                value="{{ old('category', $inventoryItem->category) }}"
                required
            >
        </div>

        <br>

        <div>
            <label for="stock">Stok</label>
            <input
                type="number"
                id="stock"
                name="stock"
                value="{{ old('stock', $inventoryItem->stock) }}"
                min="0"
                required
            >
        </div>

        <br>

        <div>
            <label for="price">Harga</label>
            <input
                type="number"
                id="price"
                name="price"
                value="{{ old('price', $inventoryItem->price) }}"
                min="0"
                step="0.01"
                required
            >
        </div>

        <br>

        <button type="submit">Simpan Perubahan</button>
        <a href="{{ route('inventory.index') }}">Batal</a>
    </form>
</body>
</html>