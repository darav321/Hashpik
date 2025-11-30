<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hashpik Signup</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="relative min-h-screen
    before:content-[''] before:absolute before:inset-0 
    before:bg-[radial-gradient(circle_at_center,#FF7112,transparent)]
    before:opacity-30 before:mix-blend-multiply
    bg-white flex justify-center items-center">
    <div class="flex flex-col justify-center items-center gap-6 w-full">
        <h1 class="text-5xl text-slate-800 font-bold">Welcome to Hashpik</h1>
        <form action="" class="flex bg-[#FFFCF8] z-10 flex-col items-center px-10 py-10 w-[90%] sm:w-1/2 lg:w-1/3 shadow-lg bg-white gap-4 rounded-lg">
            <div class="w-full flex flex-col gap-1">
                <h1 class="text-3xl text-slate-800 font-bold">Sign up</h1>
                <p class="font-medium text-sm text-slate-500">All fields are required</p>
            </div>
            <div class="flex flex-col gap-2 w-full">
                <label for="name">Name:</label>
                <input type="text" name="name" class="w-full border-2 border-slate-400 focus:border-black px-4 py-2 rounded-lg outline-none">
            </div>
            <div class="flex flex-col gap-2 w-full">
                <label for="email">Email:</label>
                <input type="email" name="email" class="w-full border-2 border-slate-400 focus:border-black px-4 py-2 rounded-lg outline-none">
            </div>
            <div class="flex flex-col gap-2 w-full">
                <label for="password">Password:</label>
                <input type="password" name="password" class="w-full border-2 border-slate-400 focus:border-black px-4 py-2 rounded-lg outline-none">
            </div>
            <button class="bg-blue-500 hover:bg-blue-600 px-5 py-2 text-white rounded-md mt-2 cursor-pointer">Submit</button>
        </form>
    </div>

</body>
</html>