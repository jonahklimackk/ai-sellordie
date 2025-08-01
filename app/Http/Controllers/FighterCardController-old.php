<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenAI\Client as OpenAIClient;

class FighterCardController extends Controller
{
    protected OpenAIClient $openai;

    public function __construct(OpenAIClient $openai)
    {
        $this->openai = $openai;
    }

    public function index()
    {
        return view('fighter-card-generator');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'website_url' => 'required|url',
            'description' => 'required|string|max:300',
            'theme'       => 'nullable|string|max:100',
        ]);

        // 1) Single prompt: fetch URL + description → generate headline/body → inject into HTML snippet
        $prompt = <<<PROMPT
You are both a web crawler and a marketing copywriter.  
1) Visit "{$request->website_url}", use the short description "{$request->description}" to understand the product/service.  
2) Write a punchy headline.  
3) Write a 1–2 sentence ad body.  
4) Embed those exact strings into this HTML (Tailwind CSS) structure—replacing [HEADLINE] and [BODY] literally:

<div class="max-w-md bg-white rounded-xl overflow-hidden shadow-lg flex flex-col">
  <div class="p-5 flex items-start gap-4">
    <img src="{{ \$ads[0]->user->profile_photo_url }}" alt="Avatar" class="w-20 h-20 rounded-full object-cover shadow-md float-left">
    <h2 class="text-5xl font-bold">[HEADLINE]</h2>
  </div>
  <div class="px-5 pb-4 flex-grow">
    <p class="text-sm leading-relaxed">[BODY]</p>
  </div>
  <div class="bg-gray-100 text-gray-700 px-5 py-3 text-sm flex justify-between items-center rounded-full">
    <span>By: {{ \$ads[0]->user->name ?? 'Unknown' }}</span>
    <a href="{$request->website_url}" class="text-yellow-500 hover:text-yellow-600 font-bold">👊 Stats</a>
  </div>
</div>

Return **only** the filled‐in HTML snippet, with `[HEADLINE]` and `[BODY]` replaced by the generated text. Don’t include any extra wrappers or explanations.
PROMPT;

        $response = $this->openai->chat()->create([
            'model'    => config('openai.default_model', 'gpt-3.5-turbo'),
            'messages' => [
                ['role' => 'system', 'content' => 'You crawl websites for context and generate exact HTML snippets with Tailwind CSS.'],
                ['role' => 'user',   'content' => $prompt],
            ],
        ]);

        $cardHtml = $response->choices[0]->message->content;

        // 2) (Optional) Generate a themed background image
        $imageUrl = null;
        if ($request->theme) {
            $img = $this->openai->images()->create([
                'model'  => 'dall-e-3',
                'prompt' => "A dynamic background in the style of {$request->theme}",
                'size'   => '512x512',
            ]);
            $imageUrl = $img->data[0]->url;
        }

        return view('fighter-card-generator', compact('cardHtml', 'imageUrl'));
    }
}
