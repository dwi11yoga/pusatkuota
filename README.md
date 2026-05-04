# 📱 JualKuota — Pulsa & Data Store

> Aplikasi web jual beli pulsa dan kuota internet yang dibangun dengan Laravel Livewire dan Tailwind CSS.
>
> A web-based mobile credit and internet data store built with Laravel Livewire and Tailwind CSS.



## 🇮🇩 Deskripsi

**JualKuota** adalah aplikasi web yang memudahkan pengelolaan dan pembelian pulsa serta kuota internet. Dirancang untuk dua tipe pengguna — **pengunjung** yang dapat melihat dan memesan produk, serta **admin** yang mengelola seluruh data produk secara efisien.

Aplikasi ini menonjolkan fitur **mass action** — admin dapat menambah, mengedit, dan menghapus banyak produk sekaligus tanpa perlu membuka satu per satu, sehingga pengelolaan produk menjadi jauh lebih cepat dan efisien.

## 🇬🇧 Description

**JualKuota** is a web application that simplifies the management and purchase of mobile credit and internet data packages. Designed for two types of users — **visitors** who can browse and order products, and **admins** who manage all product data efficiently.

The application highlights a **mass action** feature — admins can add, edit, and delete multiple products at once without opening them one by one, making product management significantly faster and more efficient.



## ✨ Fitur Unggulan / Key Features

### 🇮🇩
- **Mass Tambah Produk** — Tambahkan banyak produk sekaligus dalam satu form
- **Mass Edit** — Edit beberapa produk sekaligus, termasuk nama, modal, keuntungan, dan harga jual
- **Mass Hapus** — Hapus beberapa produk sekaligus dengan konfirmasi keamanan
- **Filter & Sortir** — Filter produk berdasarkan tipe, provider, kategori, dan rentang harga
- **Kalkulasi Otomatis** — Harga jual dihitung otomatis dari modal + keuntungan
- **Ketersediaan Produk** — Admin dapat menandai produk sebagai tersedia atau tidak tersedia
- **Tampilan Dinamis** — Mode tampilan berbeda untuk pengunjung dan admin
- **Paginasi** — Navigasi produk dengan pagination yang efisien

### 🇬🇧
- **Mass Add Products** — Add multiple products at once in a single form
- **Mass Edit** — Edit multiple products simultaneously, including name, cost, profit, and selling price
- **Mass Delete** — Delete multiple products at once with a safety confirmation dialog
- **Filter & Sort** — Filter products by type, provider, category, and price range
- **Auto Calculation** — Selling price is automatically calculated from cost + profit
- **Product Availability** — Admin can mark products as available or unavailable
- **Dynamic View** — Different display modes for visitors and admins
- **Pagination** — Efficient product navigation with pagination



## 👤 Tipe Pengguna / User Roles

| Fitur / Feature | Pengunjung / Visitor | Admin |
|---|:---:|:---:|
| Lihat produk / View products | ✅ | ✅ |
| Filter & sort produk | ✅ | ✅ |
| Tambah produk / Add products | ❌ | ✅ |
| Edit produk / Edit products | ❌ | ✅ |
| Hapus produk / Delete products | ❌ | ✅ |
| Mass action | ❌ | ✅ |
| Kelola ketersediaan / Manage availability | ❌ | ✅ |

---

## 🛠️ Tech Stack

| Layer | Technology |
|---|---|
| Backend | [Laravel](https://laravel.com) |
| Frontend | [Laravel Livewire v4](https://livewire.laravel.com) |
| Styling | [Tailwind CSS](https://tailwindcss.com) |
| Icons | [Lucide Icons](https://lucide.dev) |
| Database | MySQL / MariaDB |