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
                $.ajax({
                    url:'/search?q='+cari+'&rank='+rank,
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
        
    {{-- <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .navbar {
            background: linear-gradient(120deg, #71b280, #134e5e);
            color: white;
        }
        .navbar-brand {
            font-weight: bold;
            color: white;
        }
        .search-container {
            padding: 50px 0;
            text-align: center;
            background: linear-gradient(120deg, #11998e, #38ef7d);
            color: white;
        }
        .input-group {
            max-width: 600px;
            margin: 0 auto;
        }
        #content {
            padding: 20px;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }
        .book-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 15px;
            margin: 10px;
            width: 300px;
            text-align: center;
        }
    </style> --}}

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
        <form class="row g-3 justify-content-center mb-4" onsubmit="return false">
            <div class="col-md-6">
                <input type="text" id="cari" class="form-control" placeholder="Masukkan kata kunci">
            </div>
            <div class="col-auto">
                <select id="rank" class="form-select">
                    <option value="5">5 hasil</option>
                    <option value="10">10 hasil</option>
                </select>
            </div>
            <div class="col-auto">
                <button id="search" class="btn btn-primary">Search</button>
            </div>
        </form>

        <!-- Tempat Hasil Pencarian -->
        <div id="content" class="mt-4"></div>
    </div>

    <!-- Bootstrap JS (optional, for dropdowns etc.) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
