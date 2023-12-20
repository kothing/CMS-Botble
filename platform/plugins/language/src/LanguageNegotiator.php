<?php

namespace Botble\Language;

use Illuminate\Http\Request;
use Locale;

class LanguageNegotiator
{
    protected array $supportedLanguages = [];

    protected bool $useIntl = false;

    public function __construct(protected string $defaultLocale, array $supportedLanguages, protected Request $request)
    {
        if (extension_loaded('intl') && class_exists('Locale')) {
            $this->useIntl = true;

            foreach ($supportedLanguages as $key => $supportedLanguage) {
                if (! isset($supportedLanguage['lang'])) {
                    $supportedLanguage['lang'] = Locale::canonicalize($key);
                } else {
                    $supportedLanguage['lang'] = Locale::canonicalize($supportedLanguage['lang']);
                }
                if (isset($supportedLanguage['regional'])) {
                    $supportedLanguage['regional'] = Locale::canonicalize($supportedLanguage['regional']);
                }

                $this->supportedLanguages[$key] = $supportedLanguage;
            }
        } else {
            $this->supportedLanguages = $supportedLanguages;
        }
    }

    /**
     * Negotiates language with the user's browser through the Accept-Language
     * HTTP header or the user's host address.  Language codes are generally in
     * the form "ll" for a language spoken in only one country, or "ll-CC" for a
     * language spoken in a particular country.  For example, U.S. English is
     * "en-US", while British English is "en-UK".  Portuguese as spoken in
     * Portugal is "pt-PT", while Brazilian Portuguese is "pt-BR".
     *
     * This function is based on negotiateLanguage from Pear HTTP2
     * http://pear.php.net/package/HTTP2/
     *
     * Quality factors in the Accept-Language: header are supported, e.g.:
     *      Accept-Language: en-UK;q=0.7, en-US;q=0.6, no, dk;q=0.8
     *
     * @return string The negotiated language result or app.locale.
     */
    public function negotiateLanguage(): string
    {
        $matches = $this->getMatchesFromAcceptedLanguages();
        foreach ($matches as $key => $match) {
            if (! empty($this->supportedLanguages[$key])) {
                return $key;
            }

            if ($this->useIntl) {
                $key = Locale::canonicalize($key);
            }

            // Search for acceptable locale by 'regional' => 'af_ZA' or 'lang' => 'af-ZA' match.
            foreach ($this->supportedLanguages as $keySupported => $locale) {
                if ((isset($locale['regional']) && $locale['regional'] == $key) || (isset($locale['lang']) && $locale['lang'] == $key)) {
                    return $keySupported;
                }
            }
        }

        // If any (i.e. "*") is acceptable, return the first supported format
        if (isset($matches['*'])) {
            reset($this->supportedLanguages);

            return key($this->supportedLanguages);
        }

        if ($this->useIntl && ! empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $httpAcceptLanguage = Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']);

            if (! empty($this->supportedLanguages[$httpAcceptLanguage])) {
                return $httpAcceptLanguage;
            }
        }

        if ($this->request->server('REMOTE_HOST')) {
            $remoteHost = explode('.', $this->request->server('REMOTE_HOST'));
            $lang = strtolower(end($remoteHost));

            if (! empty($this->supportedLanguages[$lang])) {
                return $lang;
            }
        }

        return $this->defaultLocale;
    }

    /**
     * Return all the accepted languages from the browser.
     *
     * @return array Matches from the header field Accept-Languages
     */
    protected function getMatchesFromAcceptedLanguages(): array
    {
        $matches = [];

        if ($acceptLanguages = $this->request->header('Accept-Language')) {
            $acceptLanguages = explode(',', $acceptLanguages);

            $genericMatches = [];
            foreach ($acceptLanguages as $option) {
                $option = array_map('trim', explode(';', $option));
                $la = $option[0];
                if (isset($option[1])) {
                    $qa = (float)str_replace('q=', '', $option[1]);
                } else {
                    $qa = null;
                    // Assign default low weight for generic values
                    if ($la == '*/*') {
                        $qa = 0.01;
                    } elseif (str_ends_with($la, '*')) {
                        $qa = 0.02;
                    }
                }
                // Unweighted values, get high weight by their position in the
                // list
                $qa = $qa ?? 1000 - count($matches);
                $matches[$la] = $qa;

                //If for some reason the Accept-Language header only sends language with country
                //we should make the language without country an accepted option, with a value
                //less than its parent.
                $lOps = explode('-', $la);
                array_pop($lOps);
                while (! empty($lOps)) {
                    //The new generic option needs to be slightly less important than it's base
                    $qa -= 0.001;
                    $op = implode('-', $lOps);
                    if (empty($genericMatches[$op]) || $genericMatches[$op] > $qa) {
                        $genericMatches[$op] = $qa;
                    }
                    array_pop($lOps);
                }
            }
            $matches = array_merge($genericMatches, $matches);

            arsort($matches, SORT_NUMERIC);
        }

        return $matches;
    }
}
