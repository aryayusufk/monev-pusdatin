<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateLoginRunningTextRequest;
use App\Models\Setting;

class SettingController extends Controller
{
    public function editLoginRunningText()
    {
        $enabled = Setting::where('key', 'ui.login_running_text.enabled')->value('value');
        $content = Setting::where('key', 'ui.login_running_text.content')->value('value');

        return view('settings.login-running-text', [
            'enabled' => (bool) ($enabled ?? false),
            'content' => (string) ($content ?? ''),
        ]);
    }

    public function updateLoginRunningText(UpdateLoginRunningTextRequest $request)
    {
        $data = $request->validated();

        Setting::updateOrCreate(
            ['key' => 'ui.login_running_text.enabled'],
            ['value' => (bool) $data['enabled']]
        );

        Setting::updateOrCreate(
            ['key' => 'ui.login_running_text.content'],
            ['value' => (string) ($data['content'] ?? '')]
        );

        return back()->with('success', 'Running text login berhasil diperbarui.');
    }
}
