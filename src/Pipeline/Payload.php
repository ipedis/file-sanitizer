<?php

namespace Ipedis\FileSanitizer\Pipeline;

class Payload
{
    private function __construct(private string $content, public readonly string $originalContent)
    {
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public static function build(string $content): self
    {
        return new self(content: $content, originalContent: $content);
    }
}
