<?php

namespace ItkDev\TermsBundle\Twig;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TermsExtension extends AbstractExtension
{
    /** @var Environment  */
    private $twig;

    /** @var array  */
    private $configuration;

    public function __construct(Environment $twig, array $configuration)
    {
        $this->twig = $twig;
        $this->configuration = $configuration;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('terms_render', [$this, 'renderTerms'], ['is_safe' => ['html']]),
        ];
    }

    public function renderTerms() {
        $filename = $this->configuration['user_terms_content_path'] ?? null;
        if (null === $filename) {
            throw new \RuntimeException('Parameter terms_bundle.user_terms_content_path is not set');
        }
        if (!file_exists($filename)) {
            throw new \RuntimeException(sprintf('File %s does not exist', $filename));
        }
        $template = file_get_contents($filename);
        $content = $this->twig->createTemplate($template)->render();

        return $content;
    }
}
