<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>SearchBook</title>
    
    
    <link href="{{ asset('css/app.css') }}" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="{{ asset('js/custom.js') }}"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        $(document).ready(function() {
            $("#search").click(function(){
                var cari = $("#cari").val();
                var rank = $("#rank").val();
                var tahun = $("#tahun").val();
                $.ajax({
                    url:'/search?q=' + encodeURIComponent(cari) + '&rank=' + rank + '&year=' + tahun,
                    dataType : "json",
                    success: function(data){
                        $('#content').html(data);
                    },
                    error: function(){
                        alert("Terjadi kesalahan. Coba lagi.");
                    }
                });
            });
        });
        </script>
        <style>
            body {
                background-color: #f9f9f9;
                font-family: 'Segoe UI', sans-serif;
            }
            #content .card {
                min-height: 180px;
            }
            .navbar-brand {
                font-size: 1.5rem;
            }
            .watermark {
                position: fixed;
                bottom: 15px;
                right: 20px;
                font-size: 0.85rem;
                color: #999;
                opacity: 0.4;
                z-index: 1000;
                pointer-events: none;
                user-select: none;
            }
        </style>
        

</head>
<body>
    <nav class="navbar navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">SearchSkripsi 🧠</a>
        </div>
    </nav>

    <div class="container">
        <div class="text-center mb-4">
            <h2 class="fw-semibold">Temukan Dokumen Skripsi Akademik</h2>
            <p class="text-muted">Cari judul, pilih tahun, dan atur banyaknya hasil yang ingin kamu lihat</p>
        </div>

        <form id="search-form" class="row g-2 justify-content-center align-items-center" onsubmit="return false;">
            <div class="col-md-5">
                <input type="text" class="form-control" id="cari" placeholder="Ketik kata kunci...">
            </div>

            <div class="col-md-2">
                <select id="rank" class="form-select">
                    <option value="">Semua Hasil</option>
                    <option value="5">Top 5</option>
                    <option value="10">Top 10</option>
                    <option value="20">Top 20</option>
                </select>
            </div>

            <div class="col-md-2">
                <select id="tahun" class="form-select">
                    <option value="">Semua Tahun</option>
                    @foreach ($tahunList as $tahun)
                        <option value="{{ $tahun }}">{{ $tahun }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-auto">
                <button class="btn btn-primary" id="search">🔍 Cari</button>
            </div>
        </form>

        <hr class="my-4">

        <div id="content" class="row gy-4 justify-content-center">
            <!-- Hasil pencarian akan tampil di sini -->
        </div>
    </div>

    <footer class="text-center text-muted mt-5 mb-3" style="opacity: 0.6; font-size: 0.9rem;">
        23-145 Achmad Fauzan Alif Fitrah &copy; {{ date('Y') }}
    </footer>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
