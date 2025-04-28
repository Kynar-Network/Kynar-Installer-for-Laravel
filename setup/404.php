<!-- templates/errors/404.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kynar Network - Oops! Something Happened</title>
    <link href="<?php echo asset('css/tailwind.css'); ?>" rel="stylesheet">
    <style>
        body {
            background-color: #f8fafc;
        }

        textarea {
            background-color: #000;
            color: #fff;
            padding: 10px;
            overflow-x: auto; /* Ensures horizontal scrollbar if needed */
            white-space: pre-wrap; /* Wraps text if it's too long */
            max-height: 500px; /* Sets a maximum height for the textarea */
            overflow-y: auto; /* Adds vertical scrollbar if needed */
            border: none;
            resize: none; /* Prevents resizing of the textarea */
            width: 100%;
            box-sizing: border-box;
            height: 150px;
            margin-top: 10px;
        }
    </style>
</head>

<body class="h-screen w-screen flex items-center justify-center">
    <div class="flex flex-col md:flex-row items-center justify-center px-8 text-gray-700 space-y-8 md:space-y-0 md:space-x-20 rounded-lg shadow-lg pb-6">
        <!-- Text and Button -->
        <div class="max-w-md text-center">
            <div class="text-6xl font-bold text-blue-600 mt-5">Error</div>
            <p class="text-3xl md:text-4xl font-light leading-normal mt-4">Oops! Something happened.</p>
            <textarea readonly>
<?php echo htmlspecialchars($errorMessage ?? 'The page you are looking for does not exist.'); ?>
</textarea>
            <div class="mt-8">
                <a href="#" onclick="location.reload(); return false;" class="px-6 py-3 text-sm font-medium leading-5 shadow rounded-lg focus:outline-none focus:shadow-outline-blue bg-blue-600 hover:bg-blue-700 text-white transition-colors duration-150">
                    Refresh
                </a>
            </div>

        </div>
        <div class="max-w-lg relative">
            <img src="<?php echo asset('img/cat.svg'); ?>" alt="404 Image" class="w-full h-auto">
        </div>

    </div>
</body>

</html>
