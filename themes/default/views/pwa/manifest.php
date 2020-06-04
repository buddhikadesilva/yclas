{
  "short_name": "<?=Text::truncate_html(core::config('general.site_name'), 45, NULL)?>",
  "name": "<?=core::config('general.site_name')?>",
  "icons": [
    {
      "src": "<?= Core::imagefly(Theme::get('apple-touch-icon'), 192, 192) ?>",
      "type": "image/png",
      "sizes": "192x192"
    },
    {
      "src": "<?= Core::imagefly(Theme::get('apple-touch-icon'), 512, 512) ?>",
      "type": "image/png",
      "sizes": "512x512"
    }
  ],
  "start_url": "/",
  "display": "fullscreen",
  "scope": "/"
}
