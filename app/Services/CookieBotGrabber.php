<?php

namespace App\Services;

class CookieBotGrabber
{
    const STATUS_NEW = 1;
    const STATUS_INACCESSIBLE = 2;
    const STATUS_PARSED = 3;
    const STATUS_PARSED_NONEW = 4;
    const STATUS_SKIPPED = 5;
    const STATUS_SKIPPED_NO_CBID = 6;

    const GROUP_NECESSARY = 1;
    const GROUP_PREFERENCES = 2;
    const GROUP_STATISTICS = 3;
    const GROUP_ADVERTISING = 4;
    const GROUP_UNCLASSIFIED = 5;

    private $domains;

    private $userAgents = [
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.106 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.106 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.106 Safari/537.36',
        'Mozilla/5.0 (Linux; Android 10) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.106 Mobile Safari/537.36',
        'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.106 Safari/537.36'
    ];

    private $isCookieExistCb;
    private $cookieDataInsertingCb;
    private $onDomainHandlerCb;

    public function __construct(array $domains)
    {
        $this->setDomains($domains);
    }

    public function setDomains(array $domains)
    {
        $this->domains = $domains;

        return $this;
    }

    public function run(int $itemsAmount = null)
    {
        for ($i = 0; $i < ($itemsAmount ?: count($this->domains)); $i++) {
            if ($i >= count($this->domains)) {
                break;
            }

            // Загружаем страницу
            $contents = $this->loadContents($this->domains[$i]);

            if (!$contents) {
                call_user_func($this->onDomainHandlerCb, $this->domains[$i], static::STATUS_INACCESSIBLE);
                continue;
            }

            // Находим скрипт подключения кукибота и получаем идентификатор
            $cbid = $this->extractCBID($contents);

            if (!$cbid) {
                call_user_func($this->onDomainHandlerCb, $this->domains[$i], static::STATUS_SKIPPED_NO_CBID);
                continue;
            }

            // Имеющиеся данные подставляем в url-шаблон и открываем ссылку на js-файл
            $urlTemplate = 'https://consent.cookiebot.com/%s/cc.js?referer=%s';
            $jsContents = $this->loadContents(sprintf($urlTemplate, $cbid, $this->domains[$i]));

            if (!$jsContents) {
                call_user_func($this->onDomainHandlerCb, $this->domains[$i], static::STATUS_SKIPPED);
                continue;
            }

            // Обрабатываем полученное содержимое js-файла
            $this->handleJSFile($jsContents);
            call_user_func($this->onDomainHandlerCb, $this->domains[$i], static::STATUS_PARSED);
        }
    }

    public function setCookieExistingChecking(callable $cb)
    {
        $this->isCookieExistCb = $cb;
    }

    public function setCookieDataInserting(callable $cb)
    {
        $this->cookieDataInsertingCb = $cb;
    }

    public function onDomainHandled(callable $cb)
    {
        $this->onDomainHandlerCb = $cb;
    }

    private function loadContents(string $url) : ?string
    {
        if (!preg_match('~^http~', $url)) {
            $url = "https://{$url}/";
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgents[rand(0, count($this->userAgents) - 1)]);

        $result = curl_exec($ch);

        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) >= 400) {
            return null;
        }

        curl_close($ch);

        return $result;
    }

    public function extractCBID(string $contents) : ?string
    {
        $patterns = [
            '~script.+data\-cbid=[\"\']?([0-9a-f\-]+)~',
            '~'. preg_quote("cbScript.setAttribute('data-cbid', ") . "\'([0-9a-f\-]+)\'~",
            '~'. preg_quote("https://consent.cookiebot.com/uc.js?cbid=") . '([0-9a-f\-]+)~'
        ];

        // die(var_export(substr($contents, 0, 5000)));

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $contents, $matches)) {
                break;
            }
        }

        if (!count($matches)) {
            return null;
        }

        return $matches[1];
    }

    public function handleJSFile(string $contents)
    {
        $result = [];

        $variablesMap = [
            'CookieConsentDialog.cookieTableNecessary' => static::GROUP_NECESSARY,
            'CookieConsentDialog.cookieTablePreference' => static::GROUP_PREFERENCES,
            'CookieConsentDialog.cookieTableStatistics' => static::GROUP_STATISTICS,
            'CookieConsentDialog.cookieTableAdvertising' => static::GROUP_ADVERTISING,
            'CookieConsentDialog.cookieTableUnclassified' => static::GROUP_UNCLASSIFIED,
        ];

        foreach ($variablesMap as $key => $group) {
            preg_match('~' . preg_quote($key) . '\s*\=\s*(.+);~', $contents, $matches);

            if (!count($matches)) {
                continue;
            }

            $data = json_decode(stripcslashes($matches[1]));

            if (json_last_error() !== JSON_ERROR_NONE) {
                continue;
            }

            foreach ($data as $item) {
                if ($this->isCookieExistCb) {
                    if (call_user_func($this->isCookieExistCb, $item[0])) {
                        continue;
                    }
                }

                call_user_func($this->cookieDataInsertingCb, [
                    'cookie_name' => $item[0],
                    'provider' => str_replace('<br/>', '', $item[1]),
                    'description' => $item[2],
                    'group' => $group
                ]);
            }
        }
    }
}