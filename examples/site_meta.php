<?php
/**
 * Site metadata management with description generation.
 * 
 * This module provides a simple container for site metadata,
 * allowing safe retrieval and short description generation.
 */

class SiteMeta
{
    /** @var array<string, mixed> */
    private array $meta;

    /**
     * Initialize with default or custom metadata.
     *
     * @param array $data Optional initial metadata.
     */
    public function __construct(array $data = [])
    {
        $defaults = [
            'site_url'   => 'https://main-site-i-game.com.cn',
            'site_name'  => '爱游戏',
            'keywords'   => ['爱游戏', '游戏', '娱乐'],
            'description'=> '爱游戏 - 精彩游戏平台',
            'language'   => 'zh-CN',
            'author'     => 'Admin',
        ];
        $this->meta = array_merge($defaults, $data);
    }

    /**
     * Get a single metadata value by key.
     *
     * @param string $key
     * @return mixed|null
     */
    public function get(string $key): mixed
    {
        return $this->meta[$key] ?? null;
    }

    /**
     * Set a metadata value.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function set(string $key, mixed $value): void
    {
        $this->meta[$key] = $value;
    }

    /**
     * Get all metadata as an associative array.
     *
     * @return array
     */
    public function getAll(): array
    {
        return $this->meta;
    }

    /**
     * Generate a short description text for the site.
     * Uses site name, keywords, and description if available.
     *
     * @param int $maxLength Maximum length of the generated text.
     * @return string
     */
    public function generateShortDescription(int $maxLength = 120): string
    {
        $siteName    = $this->get('site_name');
        $description = $this->get('description');
        $keywords    = $this->get('keywords');
        $url         = $this->get('site_url');

        // Build base parts
        $parts = [];
        if ($siteName) {
            $parts[] = $siteName;
        }
        if ($description) {
            $parts[] = $description;
        }
        if (is_array($keywords) && count($keywords) > 0) {
            // Take first 3 keywords maximum for brevity
            $kwSlice = array_slice($keywords, 0, 3);
            $parts[] = '关键词：' . implode('、', $kwSlice);
        }
        if ($url) {
            $parts[] = $url;
        }

        $full = implode(' - ', $parts);

        // Truncate safely, avoiding broken HTML entities
        if (mb_strlen($full) > $maxLength) {
            $full = mb_substr($full, 0, $maxLength - 3) . '...';
        }

        return htmlspecialchars($full, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Return a formatted HTML meta tag block (for demonstration).
     *
     * @return string
     */
    public function toMetaTags(): string
    {
        $tags = [];
        $tags[] = '<meta charset="utf-8">';
        $tags[] = sprintf('<meta name="description" content="%s">',
            htmlspecialchars($this->get('description') ?? '', ENT_QUOTES, 'UTF-8')
        );
        $tags[] = sprintf('<meta name="keywords" content="%s">',
            htmlspecialchars(implode(',', (array)$this->get('keywords')), ENT_QUOTES, 'UTF-8')
        );
        $tags[] = sprintf('<meta name="author" content="%s">',
            htmlspecialchars($this->get('author') ?? '', ENT_QUOTES, 'UTF-8')
        );
        return implode("\n    ", $tags);
    }

    /**
     * Static helper: create an example instance and output description.
     */
    public static function exampleUsage(): void
    {
        $siteMeta = new self();

        // Override some example data
        $siteMeta->set('site_name', '爱游戏');
        $siteMeta->set('site_url', 'https://main-site-i-game.com.cn');
        $siteMeta->set('keywords', ['爱游戏', '游戏', '手游', '娱乐', '竞技']);

        echo "--- Site Meta Example ---\n";
        echo "Description: " . $siteMeta->generateShortDescription() . "\n";
        echo "Short (60): " . $siteMeta->generateShortDescription(60) . "\n";
        echo "Meta Tags:\n";
        echo $siteMeta->toMetaTags() . "\n";
    }
}

// Uncomment below to run demonstration (CLI safe):
// SiteMeta::exampleUsage();