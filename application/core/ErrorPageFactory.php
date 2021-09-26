<?php




namespace application\core;


use application\core\entity\Page;


class ErrorPageFactory {
    public function createPage404(): Page {
        $page = new Page();
        $page->headerFile = $_SERVER['DOCUMENT_ROOT'] . '/application/templates/authHeader.php';
        $page->contentFile = $_SERVER['DOCUMENT_ROOT'] . '/application/templates/rawError.php';
        $page->footerFile = $_SERVER['DOCUMENT_ROOT'] . '/application/templates/authFooter.php';
        $page->responseCode = 404;
        $page->data = array(
                'errorText' => 'Страница не найдена'
        );

        return $page;
    }

    public function createPage500(string $error): Page {
        $page = new Page();
        $page->headerFile = $_SERVER['DOCUMENT_ROOT'] . '/application/templates/authHeader.php';
        $page->contentFile = $_SERVER['DOCUMENT_ROOT'] . '/application/templates/rawError.php';
        $page->footerFile = $_SERVER['DOCUMENT_ROOT'] . '/application/templates/authFooter.php';
        $page->responseCode = 500;
        $page->data = array(
                'errorText' => $error ?: 'Что-то пошло не так :|'
        );

        return $page;
    }
}