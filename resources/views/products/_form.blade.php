@csrf

@if ($errors->any())
    <div class="error-list">
        <strong>Data belum valid.</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="form-grid">
    <div class="field">
        <label for="nama_produk">Nama Produk</label>
        <input id="nama_produk" name="nama_produk" type="text" value="{{ old('nama_produk', $product->nama_produk ?? '') }}" required>
    </div>

    <div class="field">
        <label for="active">Status</label>
        <select id="active" name="active" required>
            <option value="1" @selected((string) old('active', isset($product) ? (int) $product->active : 1) === '1')>Aktif</option>
            <option value="0" @selected((string) old('active', isset($product) ? (int) $product->active : 1) === '0')>Nonaktif</option>
        </select>
    </div>

    <div class="field-full">
        <label for="deskripsi_produk">Deskripsi Produk</label>
        <textarea id="deskripsi_produk" name="deskripsi_produk">{{ old('deskripsi_produk', $product->deskripsi_produk ?? '') }}</textarea>
    </div>

    <div class="field-full">
        <label for="images">Upload Gambar Produk</label>
        <input id="images" name="images[]" type="file" multiple accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
        <p class="meta file-help">Bisa upload beberapa gambar sekaligus. Gambar pertama akan jadi gambar utama jika produk belum punya gambar.</p>

        @if (!empty($product?->images) && $product->images->isNotEmpty())
            <div class="stack muted-panel">
                <p class="meta">Kelola gambar yang sudah ada:</p>
                <div class="gallery-editor">
                    @foreach ($product->images as $image)
                        <div class="gallery-card">
                            <img src="{{ $image->image_url }}" alt="{{ $product->nama_produk }}">
                            <div class="gallery-controls">
                                <label class="control-chip">
                                    <input type="radio" name="primary_image_id" value="{{ $image->id }}"
                                        @checked((string) old('primary_image_id', optional($product->primary_image)->id) === (string) $image->id)>
                                    Jadikan cover utama
                                </label>
                                <label class="control-chip">
                                    <input type="checkbox" name="deleted_image_ids[]" value="{{ $image->id }}"
                                        @checked(collect(old('deleted_image_ids', []))->contains((string) $image->id) || collect(old('deleted_image_ids', []))->contains($image->id))>
                                    Hapus gambar ini
                                </label>
                            </div>
                            <div class="gallery-meta">
                                <div>
                                    <strong>Hash</strong>
                                    <span>{{ $image->hash_img ?: '-' }}</span>
                                </div>
                                <div>
                                    <strong>Path</strong>
                                    <span>{{ $image->image_path }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @elseif (!empty($product?->hash_img))
            <div class="muted-panel">
                <p class="meta">Hash gambar utama saat ini: {{ $product->hash_img }}</p>
            </div>
        @endif
    </div>

    <div class="field">
        <label for="created_by">Created By</label>
        <input id="created_by" name="created_by" type="text" value="{{ old('created_by', $product->created_by ?? '') }}">
    </div>

    <div class="field">
        <label for="update_by">Update By</label>
        <input id="update_by" name="update_by" type="text" value="{{ old('update_by', $product->update_by ?? '') }}">
    </div>
</div>

<div class="actions">
    <a href="{{ route('products.index') }}" class="button secondary">Kembali</a>
    <button type="submit">{{ $submitLabel }}</button>
</div>
