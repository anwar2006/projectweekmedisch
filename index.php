<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/pep/0.4.3/pep.js" integrity="sha256-yZpZULjaPllFSRFfS6JsDvucyRd3yNo7yKc/YsMQAsk=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <title>Page</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        /* Tijdelijk om spacing te fixen bij content? */
        main {
            margin-top: 4rem;
            margin-left: 16rem; 
            width: calc(100% - 16rem); 
            height: calc(100vh - 4rem);
            overflow-y: auto; 
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Sidebar -->
    <aside class="fixed left-0 top-0 h-full w-64 bg-slate-900 text-white p-5 z-20">
    <!--Tekst font is bold en de tekst is 3 keer zo groot.-->
        <p class="font-bold text-3xl">Apothecare<p><br>
    <!--De cursor is een pointer en de tekst is 1 keer zo groot.-->
        <p class="cursor-pointer text-xl">Administrator</p><br>
    <!--De cursor is op pointer gezet, tekst wordt teal wanneer je er overheen gaat en dat is een transistion.-->
        <p class="cursor-pointer hover:text-teal-700 transistion-colors">Dashboard</p>
        <p class="cursor-pointer hover:text-teal-700 transistion-colors">Orders</p>
        <p class="cursor-pointer hover:text-teal-700 transistion-colors">Customers</p>
        <p class="cursor-pointer hover:text-teal-700 transistion-colors">Inventory</p>
        <p class="cursor-pointer hover:text-teal-700 transistion-colors">Analytics</p>
        <p class="cursor-pointer hover:text-teal-700 transistion-colors">Settings</p>
    </aside>

    <!-- Topnav -->
    <header class="fixed top-0 left-64 right-0 h-16 bg-white shadow-md flex items-center px-6 z-10">
        <input type="text" placeholder="Search..." class="w-96 h-10 px-4 rounded-lg bg-gray-100 border">
    </header>

    <!-- Main content -->
    <main class="p-6">
        <!-- Cards Row -->
        <div class="flex gap-4">
    <!--De breedte is 70, de hoogte is 40, achtergrond is grijs, de padding is 5 en de hoeken zijn rond.-->
            <div class="w-70 h-40 bg-gray-300 p-5 rounded-lg"></div>
            <div class="w-70 h-40 bg-gray-300 p-5 rounded-lg"></div>
            <div class="w-70 h-40 bg-gray-300 p-5 rounded-lg"></div>
            <div class="w-70 h-40 bg-gray-300 p-5 rounded-lg"></div>
        </div>

        <div class="flex pt-6">
    <!--breedte is 2780, hoogte is 40, achtergrond is grijs en de hoeken zijn rond.-->
            <div class="w-2780 h-40 bg-gray-300 rounded-lg"></div>
        </div>

        <div class="flex gap-2 pt-6">
    <!--breedte is 145, hoogte is 40, achtergrond is grijs, de padding is 5 en de hoeken zijn rond.-->
            <div class="w-145 h-40 bg-gray-300 p-5 rounded-lg"></div>
            <div class="w-145 h-40 bg-gray-300 p-5 rounded-lg"></div>
        </div>
    </main>
</body>
</html>