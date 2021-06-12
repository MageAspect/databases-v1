<?php


namespace application\core;


class Page {
    public int $responseCode;
    public string $headerFile;
    public string $title;
    public array $data;
    public string $contentFile;
    public string $footerFile;
}