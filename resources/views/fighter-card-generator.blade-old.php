@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6">Fighter Card Generator</h1>

    <form action="{{ route('fighter-card.generate') }}" method="POST" class="space-y-6">
        @csrf
        <div>
            <label class="block text-sm font-bold mb-1">Website URL</label>
            <input type="url" name="website_url" class="w-full p-2 rounded text-black" required>
        </div>
        <div>
            <label class="block text-sm font-bold mb-1">Short Description</label>
            <textarea name="description" rows="3" class="w-full p-2 rounded text-black" required></textarea>
        </div>
        <div>
            <label class="block text-sm font-bold mb-1">Theme (optional)</label>
            <input type="text" name="theme" class="w-full p-2 rounded text-black">
        </div>
        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Generate Ad Card</button>
    </form>

    @if (!empty($cardHtml))
    <div class="mt-10 border-t pt-10">
        <h2 class="text-xl font-bold mb-4">Your Generated Ad Card</h2>
        @if ($imageUrl)
        <img src="{{ $imageUrl }}" alt="Background Image" class="mb-4 rounded shadow">
        @endif
        {!! $cardHtml !!}
    </div>
    @endif

</div>
@endsection