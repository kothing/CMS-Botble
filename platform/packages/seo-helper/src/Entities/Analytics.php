<?php

namespace Botble\SeoHelper\Entities;

use Botble\SeoHelper\Contracts\Entities\AnalyticsContract;

class Analytics implements AnalyticsContract
{
    protected string|null $google = '';

    public function setGoogle($code): static
    {
        $this->google = $code;

        return $this;
    }

    public function render(): string
    {
        return implode(PHP_EOL, array_filter([
            $this->renderGoogleScript(),
        ]));
    }

    public function __toString()
    {
        return $this->render();
    }

    protected function renderGoogleScript(): string
    {
        if (empty($this->google)) {
            return '';
        }

        return <<<EOT
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async defer src="https://www.googletagmanager.com/gtag/js?id=$this->google"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', '$this->google');
</script>
EOT;
    }
}
