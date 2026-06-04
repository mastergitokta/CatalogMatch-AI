# CatalogMatch-AI


CatalogMatch-AI adalah sistem pencocokan gambar produk berbasis Laravel yang memungkinkan user mengirim gambar produk melalui Telegram Bot, lalu sistem akan mencocokkan gambar tersebut dengan katalog produk yang ada di database.

Project ini cocok digunakan untuk toko online, katalog produk, WhatsApp/Telegram commerce, reseller system, atau SaaS yang membutuhkan fitur pencarian produk berdasarkan gambar.

## Fitur Utama

- Menerima gambar dari user melalui Telegram Bot
- Mengunduh gambar dari Telegram Bot API
- Membuat image hash dari gambar yang dikirim user
- Mencocokkan gambar dengan gambar produk di katalog
- Menggunakan perceptual hashing untuk mendeteksi gambar yang mirip
- Mengembalikan daftar produk terdekat berdasarkan similarity score
- Cocok untuk gambar hasil screenshot atau gambar katalog yang sama/mirip

## Use Case

Contoh penggunaan:

1. User mengirim gambar produk ke Telegram Bot
2. Laravel menerima webhook dari Telegram
3. Sistem mengunduh gambar dari Telegram
4. Sistem membuat image hash dari gambar tersebut
5. Sistem membandingkan hash gambar dengan hash produk di katalog
6. Sistem mengirimkan hasil produk yang paling mirip ke user

Flow sederhana:

```text
User kirim gambar
        ↓
Telegram Bot Webhook
        ↓
Laravel Download Image
        ↓
Generate Image Hash
        ↓
Match dengan Katalog
        ↓
Bot balas produk terdekat
