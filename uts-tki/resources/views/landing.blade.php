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

</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">23-145 Achmad Fauzan Alif Fitrah</a>
        </div>
    </nav>

    <!-- Form -->
    <div class="container"> 
        <form onsubmit="return false;">
            <input type="text" id="cari" placeholder="Cari judul skripsi...">
            <select id="rank">
                <option value="">Semua Skripsi</option>
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="20">20</option>
            </select>
            <select id="tahun" class="form-control mx-2">
                <option value="">Semua Tahun</option>
                @foreach ($tahunList as $tahun)
                    <option value="{{ $tahun }}">{{ $tahun }}</option>
                @endforeach
            </select>            
            <button id="search">Search</button>
        </form>        

        <!-- Tempat Hasil Pencarian -->
        <div id="content" class="mt-4"></div>
    </div>

    <!-- Bootstrap JS (optional, for dropdowns etc.) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
