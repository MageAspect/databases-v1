<?php

/**
 * @author Mark Prohorov <mark@intervolga.ru>
 */


namespace application\core;


use application\core\exception\RenderException;


class View {

    /**
     * Сборка и отрисовка страницы
     * @throws RenderException
     */
    public function render(Page $page) {
        $title = $page->title;
        $pageData = $page->data;

        if (!file_exists($page->headerFile)) {
            throw new RenderException('Header файл: ' . $page->headerFile . ' не найден!');
        }
        if (!file_exists($page->contentFile)) {
            throw new RenderException('Сontent файл: ' . $page->contentFile . ' не найден!');
        }
        if (!file_exists($page->footerFile)) {
            throw new RenderException('Footer файл: ' . $page->footerFile . ' не найден!');
        }
        require $page->headerFile;
        require $page->contentFile;
        require $page->footerFile;
    }

    public function redirect($url) {
        header('Location: ' . $url);
        die();
    }
}