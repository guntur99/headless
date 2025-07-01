<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageSwitcher extends Component
{
    public function switchLanguage($locale)
    {
        $localeValue = '';
        if (array_key_exists($locale, config('app.available_locales'))) {
            $localeValue = $locale;
        } else {
            $localeValue = config('app.locale');
        }
        Session::put('locale', $localeValue);
        App::setLocale($localeValue);

        return redirect(request()->header('Referer'));
    }

    public function render()
    {
        return view('livewire.language-switcher');
    }
}
