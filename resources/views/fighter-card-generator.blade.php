{{-- resources/views/fighter-card-generator.blade.php --}}

@extends('layouts.app')

@section('content')
  <div class="container mx-auto py-8">
    {{-- Form --}}
    <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow">
      <h1 class="text-2xl font-bold mb-6">Fighter Card Generator</h1>

      <form method="POST" action="{{ route('fighter-card.generate') }}" class="space-y-6">
        @csrf

        <div>
          <label class="block text-sm font-medium mb-1">Website URL</label>
          <input
            type="url"
            name="website_url"
            value="{{ old('website_url') }}"
            required
            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
          >
          @error('website_url')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label class="block text-sm font-medium mb-1">Instructions for the AI (optional)</label>
          <textarea
            name="instructions_for_ai"
            rows="3"
            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
          >{{ old('instructions_for_ai') }}</textarea>
          @error('instructions_for_ai')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label class="block text-sm font-medium mb-1">Theme (optional)</label>
          <input
            type="text"
            name="theme"
            value="{{ old('theme') }}"
            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
          >
          @error('theme')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
          @enderror
        </div>

        <div class="flex justify-end">
          <button
            type="submit"
            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            Generate Ad Card
          </button>
        </div>
      </form>
    </div>

    {{-- Generated WYSIWYG Snippet --}}
    @if(! empty($cardHtml))
      <div class="max-w-2xl mx-auto mt-10 space-y-6">
        {{-- Optional background image --}}
        @if(! empty($imageUrl))
          <img
            src="{{ $imageUrl }}"
            alt="Background"
            class="w-full rounded-lg shadow mb-6"
          >
        @endif

        {{-- Card + Copy Rich‑Text Button --}}
        <div class="relative bg-white rounded-lg shadow-lg overflow-hidden p-6">
          <button
            id="copy-rich-button"
            class="absolute top-4 right-4 bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded z-10"
          >
            Copy Card
          </button>

          {{-- Rendered WYSIWYG Content --}}
          <div id="card-content" class="prose">
            {!! $cardHtml !!}
          </div>
        </div>
      </div>

      {{-- Copy‑to‑clipboard Script with computed‑color inlining --}}
      <script>
        document.getElementById('copy-rich-button').addEventListener('click', async function() {
          const original = document.getElementById('card-content');
          const clone    = original.cloneNode(true);

          // Inline computed text colors from the live DOM, then strip classes
          inlineTextColor(original, clone);

          const html = clone.innerHTML.trim();
          const text = clone.innerText.trim();

          const item = new ClipboardItem({
            'text/html':  new Blob([html], { type: 'text/html' }),
            'text/plain': new Blob([text], { type: 'text/plain' })
          });

          try {
            await navigator.clipboard.write([item]);
            this.textContent = 'Copied!';
            setTimeout(() => this.textContent = 'Copy Card', 1500);
          } catch (err) {
            console.error('Copy failed', err);
            alert('Unable to copy rich text. Please try again.');
          }
        });

        function inlineTextColor(original, clone) {
          // Collect matching NodeLists of original & clone, including root
          const origNodes  = [original, ...original.querySelectorAll('*')];
          const cloneNodes = [clone,    ...clone.querySelectorAll('*')];

          origNodes.forEach((origEl, idx) => {
            const cloneEl = cloneNodes[idx];
            const cs      = window.getComputedStyle(origEl);

            // Apply the computed color as inline style
            if (cs.color) {
              cloneEl.style.color = cs.color;
            }
            // Remove all classes so pasted HTML relies only on inline styles
            cloneEl.className = '';
          });
        }
      </script>
    @endif
  </div>
@endsection
