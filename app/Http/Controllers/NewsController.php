<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateRunningTextRequest;
use App\Models\News;

class NewsController extends Controller
{
    public function editRunningText()
    {
        $enabled = News::where('key', 'ui.running_text.enabled')->value('value');
        $content = News::where('key', 'ui.running_text.content')->value('value');

        return view('news.index', [
            'enabled' => (bool) ($enabled ?? false),
            'content' => (string) ($content ?? ''),
        ]);
    }

    public function updateRunningText(UpdateRunningTextRequest $request)
    {
        $data = $request->validated();

        News::updateOrCreate(
            ['key' => 'ui.running_text.enabled'],
            ['value' => (bool) $data['enabled']]
        );

        News::updateOrCreate(
            ['key' => 'ui.running_text.content'],
            ['value' => (string) ($data['content'] ?? '')]
        );

        return back()->with('success', 'Berita di halaman login berhasil diperbarui.');
    }
}
