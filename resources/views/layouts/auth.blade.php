<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title') | TÜRKYAN</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            /* İstediğiniz ilk tasarıma uygun arka plan */
            background-image: 
                linear-gradient(rgba(10, 10, 10, 0.7), rgba(10, 10, 10, 0.9)),
                url("{{ asset('images/auth-bg.jpg') }}");
            
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        /* Panel stili, blur efekti olmadan, daha net ve koyu bir panele dönüştürüldü */
        .auth-panel {
            background-color: rgba(18, 18, 18, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }

        .form-input {
            background-color: rgba(10, 10, 10, 0.7);
            border: 1px solid #4A5568;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-input:focus {
            outline: none;
            border-color: #F97316;
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.5);
        }
    </style>
</head>
<body class="text-white">

    <div class="min-h-screen flex flex-col items-center justify-center p-4">
        @yield('content')
    </div>
    
</body>
</html>