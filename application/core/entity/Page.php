<?php


namespace application\core\entity;


class Page {
    public int $responseCode = 0;
    public string $headerFile = '';
    public string $title = '';
    public array $data = array();
    public string $sidebarFile = '';
    public string $contentFile = '';
    public string $footerFile = '';
}