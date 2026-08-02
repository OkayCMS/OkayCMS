<?php

namespace Okay\Core;

class JsSocial
{
    /**
     * Домен некоторых соц. сетей не соответствует стилям font-awesome, для них сделаны эти алиасы
     *
     * @var string[]
     */
    private static $socialAliases = [
        "ok" => 'odnoklassniki',
    ];

    /**
     * Мережі поширення для share_links.tpl та адмінки: id, label, logo (ім'я SVG у темі), shareUrl, опційно shareUrlMobile.
     * Плейсхолдери в URL: {url}, {title}.
     *
     * @var array<string, array{id: string, label: string, logo: string, shareUrl: string, shareUrlMobile?: string}>
     */
    private array $shareNetworks = [
        'facebook' => [
            'id' => 'facebook',
            'label' => 'Facebook',
            'logo' => 'facebook',
            'shareUrl' => 'https://www.facebook.com/sharer/sharer.php?u={url}',
        ],
        'telegram' => [
            'id' => 'telegram',
            'label' => 'Telegram',
            'logo' => 'telegram',
            'shareUrl' => 'https://t.me/share/url?url={url}&text={title}',
        ],
        'viber' => [
            'id' => 'viber',
            'label' => 'Viber',
            'logo' => 'viber',
            'shareUrl' => 'https://invite.viber.com/?text={title}%20{url}',
            'shareUrlMobile' => 'viber://forward?text={title}%20{url}',
        ],
        'whatsapp' => [
            'id' => 'whatsapp',
            'label' => 'WhatsApp',
            'logo' => 'whatsapp',
            'shareUrl' => 'https://wa.me/?text={title}%20{url}',
            'shareUrlMobile' => 'whatsapp://send?text={title}%20{url}',
        ],
        'linkedin' => [
            'id' => 'linkedin',
            'label' => 'LinkedIn',
            'logo' => 'linkedin',
            'shareUrl' => 'https://www.linkedin.com/sharing/share-offsite/?url={url}',
        ],
        'twitter' => [
            'id' => 'x-twitter',
            'label' => 'X (Twitter)',
            'logo' => 'x-twitter',
            'shareUrl' => 'https://x.com/intent/post?url={url}&text={title}',
        ],
        'threads' => [
            'id' => 'threads',
            'label' => 'Threads',
            'logo' => 'threads',
            'shareUrl' => 'https://www.threads.net/intent/post?text={title}%20{url}',
        ],
        'pinterest' => [
            'id' => 'pinterest',
            'label' => 'Pinterest',
            'logo' => 'pinterest',
            'shareUrl' => 'https://pinterest.com/pin/create/button/?url={url}&description={title}',
        ],
        'reddit' => [
            'id' => 'reddit',
            'label' => 'Reddit',
            'logo' => 'reddit',
            'shareUrl' => 'https://www.reddit.com/submit?url={url}&title={title}',
        ],
        'email' => [
            'id' => 'envelope',
            'label' => 'Email',
            'logo' => 'envelope',
            'shareUrl' => 'mailto:?subject={title}&body={url}',
        ],
    ];

    /**
     * Повертає масив мереж поширення для share_links.tpl та адмінки (id, label, logo, shareUrl, shareUrlMobile).
     * Кнопка «копіювати посилання» не входить у список — вона жорстко в шаблоні.
     *
     * @return array<int, array{id: string, label: string, logo: string, shareUrl: string, shareUrlMobile?: string}>
     */
    public function getShareNetworks(): array
    {
        return array_values($this->shareNetworks);
    }

    public static function getSocialDomain($link)
    {
        $socialDomain = preg_replace('~^(https?://)?(www\.)?([^.]+)?\..*$~', '$3', $link);

        if (isset(self::$socialAliases[$socialDomain])) {
            return self::$socialAliases[$socialDomain];
        }
        return $socialDomain;
    }
}
