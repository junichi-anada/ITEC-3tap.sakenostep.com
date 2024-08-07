<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hamburger Menu Example</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <style>
        /* モバイルメニューのトランジション */
        .menu-enter {
            transform: translateX(100%);
        }

        .menu-enter-active {
            transform: translateX(0);
            transition: transform 1500ms;
        }

        .menu-leave-active {
            transform: translateX(100%);
            transition: transform 1500ms;
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="flex justify-between items-center p-4 bg-white shadow-md">
        <h1 class="text-lg font-bold">My Website</h1>
        <button id="menu-button" class="focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
            </svg>
        </button>
    </div>

    <!-- モバイルメニュー -->
    <div id="mobile-menu" class="fixed inset-0 bg-black bg-opacity-50 flex justify-end transform translate-x-full">
        <div class="bg-white w-64 p-4">
            <button id="close-button" class="mb-4 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
            <nav>
                <ul>
                    <li class="mb-2"><a href="#" class="text-gray-800">Home</a></li>
                    <li class="mb-2"><a href="#" class="text-gray-800">About</a></li>
                    <li class="mb-2"><a href="#" class="text-gray-800">Services</a></li>
                    <li class="mb-2"><a href="#" class="text-gray-800">Contact</a></li>
                </ul>
            </nav>
        </div>
    </div>

    <script>
        document.getElementById('menu-button').addEventListener('click', function () {
            var menu = document.getElementById('mobile-menu');
            menu.classList.remove('translate-x-full');
            menu.classList.add('menu-enter', 'menu-enter-active');
        });

        document.getElementById('close-button').addEventListener('click', function () {
            var menu = document.getElementById('mobile-menu');
            menu.classList.add('menu-leave-active');
            setTimeout(function () {
                menu.classList.remove('menu-enter', 'menu-enter-active', 'menu-leave-active');
                menu.classList.add('translate-x-full');
            }, 300);
        });
    </script>
</body>

</html>
