<?php

namespace App\Support;

use DOMDocument;
use HTMLPurifier;
use HTMLPurifier_Config;
use Illuminate\Validation\ValidationException;

class PostContent
{
    private HTMLPurifier $purifier;

    public function __construct()
    {
        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed', 'p,br,strong,em,s,u,h2,h3,ul,ol,li,blockquote,a[href|title],img[src|alt]');
        $config->set('URI.DisableExternalResources', true);
        $config->set('Cache.DefinitionImpl', null);
        $this->purifier = new HTMLPurifier($config);
    }

    public function clean(string $content): string
    {
        $content = preg_replace('~<p>\s*</p>~i', '<p><br></p>', $content);

        return $this->purifier->purify($content);
    }

    public function imageIds(string $content): array
    {
        if ($content === '') {
            return [];
        }

        $document = new DOMDocument;
        $document->loadHTML('<?xml encoding="UTF-8"><html><body>'.$content.'</body></html>', LIBXML_NOERROR | LIBXML_NOWARNING | LIBXML_NONET);
        $ids = [];

        foreach ($document->getElementsByTagName('img') as $image) {
            if (! preg_match('~^/post-images/([0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12})$~i', $image->getAttribute('src'), $matches)) {
                throw ValidationException::withMessages(['content' => '本文にはアップロードした画像のみ使用できます。']);
            }
            $ids[] = strtolower($matches[1]);
        }

        return array_values(array_unique($ids));
    }
}
