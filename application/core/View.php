<?php




namespace application\core;


use application\core\entity\Page;
use application\core\exception\RenderException;


class View {

    /**
     * Сборка и отрисовка страницы
     * @throws RenderException
     */
    public function render(Page $page) {
        $PAGE_TITLE = $page->title;
        $PAGE_DATA = $page->data;

        if (!empty($page->headerFile) && !file_exists($page->headerFile)) {
            throw new RenderException('Header файл: ' . $page->headerFile . ' не найден!');
        } elseif (!empty($page->headerFile)) {
            require $page->headerFile;
        }

        if (!empty($page->sidebarFile) && !file_exists($page->sidebarFile)) {
            throw new RenderException('Sidebar файл: ' . $page->sidebarFile . ' не найден!');
        } elseif (!empty($page->sidebarFile)) {
            require $page->sidebarFile;
        }

        if (!empty($page->contentFile) && !file_exists($page->contentFile)) {
            throw new RenderException('Content файл: ' . $page->contentFile . ' не найден!');
        } elseif (!empty($page->contentFile)) {
            require $page->contentFile;
        }

        if (!empty($page->footerFile) && !file_exists($page->footerFile)) {
            throw new RenderException('Footer файл: ' . $page->footerFile . ' не найден!');
        } elseif (!empty($page->footerFile)) {
            require $page->footerFile;
        }
    }

    public function redirect($url) {
        header('Location: ' . $url);
        die();
    }
}