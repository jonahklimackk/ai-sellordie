<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Tailwind CSS (via CDN) -->
  <script src="https://cdn.tailwindcss.com"></script>

  @if (class_exists(\Livewire\Livewire::class))
    @livewireStyles
  @endif

  <title>Fighter Card Generator</title>
</head>
<body class="bg-gray-100 font-sans">
  <div class="min-h-screen container mx-auto py-8">
    @yield('content')
  </div>

  @if (class_exists(\Livewire\Livewire::class))
    @livewireScripts
  @endif
</body>
</html>