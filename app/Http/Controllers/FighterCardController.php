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
            'website_url'         => 'required|url',
            'instructions_for_ai' => 'nullable|string|max:500',
            'theme'               => 'nullable|string|max:100',
        ]);

        $website      = $request->input('website_url');
        $instructions = $request->input('instructions_for_ai');
        $theme        = $request->input('theme');

        // optional instructions text
        $instructionText = '';
        if (! empty($instructions)) {
            $escaped = str_replace('"', '\"', $instructions);
            $instructionText = ' these instructions: "' . $escaped . '",';
        }

        // WYSIWYG template: heading + body only
        $wysiwygTemplate = <<<'HTML'
<h2 style="font-size:2rem; font-weight:bold;">[HEADLINE]</h2>
[AD_COPY]
HTML;

        // build prompt
        $prompt  = "You are a web crawler & copywriter.\n\n";
        $prompt .= "1) Visit \"{$website}\" and understand the product/service.\n";
        $prompt .= "2) Using{$instructionText} write:\n";
        $prompt .= "   • A single, punchy headline.\n";
        $prompt .= "   • **2–3 paragraphs** of persuasive ad copy, each wrapped in <p>…</p>.\n";
        $prompt .= "     Use <strong>…</strong> for bold, <em>…</em> for italics, and ";
        $prompt .= "<span style=\"color:red;\">…</span> to highlight in red.\n\n";
        $prompt .= "3) Inject your results into exactly this template—\n";
        $prompt .= "   replace [HEADLINE] and [AD_COPY] verbatim:\n\n";
        $prompt .= $wysiwygTemplate . "\n\n";
        $prompt .= "Return only the filled‑in snippet (no surrounding <div> or CSS classes).";

        // call OpenAI
        $response = $this->openai->chat()->create([
            'model'    => config('openai.default_model', 'gpt-3.5-turbo'),
            'messages' => [
                [
                    'role'    => 'system',
                    'content' => 'You crawl websites for context and generate clean WYSIWYG HTML snippets: heading, paragraphs, bold, italics, and inline color.'
                ],
                [
                    'role'    => 'user',
                    'content' => $prompt,
                ],
            ],
        ]);

        $cardHtml = $response->choices[0]->message->content;

        // optional background image (unchanged)
        $imageUrl = null;
        if (! empty($theme)) {
            $img = $this->openai->images()->create([
                'model'  => 'dall-e-3',
                'prompt' => "A dramatic fighter‑style background for an ad in the style of {$theme}",
                'size'   => '512x512',
            ]);
            $imageUrl = $img->data[0]->url;
        }

        return view('fighter-card-generator', compact('cardHtml', 'imageUrl'));
    }
}
