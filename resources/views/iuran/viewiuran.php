@extends('layouts.app')

@section('content')
<div class="page-heading">
    <h3>Iuran</h3>
    <p class="text-subtitle text-muted">
        Berikut merupakan menu dari iuran yang dapat digunakan untuk melaporkan segala iuran yang telah bapak/ibu lakukan
    </p>

    <section class="section">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Data Iuran</h4>
                <a href="/inputiuran" class="btn btn-primary float-end">Tambah Iuran</a>
                <p class="text-subtitle text-muted">
                    Berikut merupakan data seputar rapat yang bapak/ibu inputkan
                </p>
            </div>
            <div class="card-body">
                <!-- Isi tabel atau form disini -->
            </div>
        </div>
    </section>
</div>
@endsection
