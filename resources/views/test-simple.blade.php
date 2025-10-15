<!DOCTYPE html>
<html>
<head>
    <title>CSS Test</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="bg-blue-500 text-white p-8 text-center">
        <h1 class="text-4xl font-bold mb-4">🎉 CSS IS WORKING!</h1>
        <p class="text-xl">If you see this in white text on a blue background, Tailwind CSS is loaded!</p>
    </div>
    
    <div class="max-w-4xl mx-auto p-8">
        <div class="bg-white rounded-lg shadow-lg p-6 mb-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Card 1</h2>
            <p class="text-gray-600">This should be a white card with shadow.</p>
        </div>
        
        <div class="bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg p-6">
            <h2 class="text-2xl font-bold mb-2">Gradient Card</h2>
            <p>Purple to pink gradient!</p>
        </div>
    </div>
</body>
</html>

