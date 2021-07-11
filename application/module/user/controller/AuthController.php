<?php


namespace application\module\user\controller;


use application\core\exception\RenderException;
use application\core\PublicController;
use application\module\user\facade\UserFacadeException;


class AuthController extends PublicController {

    /**
     * @throws RenderException
     */
    public function logInAction() {
        $page = $this->getDefaultPage();
        $page->headerFile = $_SERVER['DOCUMENT_ROOT'] . '/application/layouts/authHeader.php';
        $page->title = 'Авторизация';
        $page->contentFile = dirname(__DIR__) . '/pages/auth.php';

        if (isset($_POST['submit_auth'])) {
            $this->submit();
        }

        $page->data = $this->dataToView;
        $this->view->render($page);
    }

    protected function submit() {
        try {
            $fields = $this->getPreparedFields($_POST);
            if ($this->validateFields($fields, $this->dataToView['errors'])) {
                $this->userFacade->authUser($fields['login'], $fields['password']);
                $this->view->redirect('/form/');
            }
        } catch (UserFacadeException $e) {
            $this->dataToView['errors'][] = $e->getMessage();
        }
    }

    protected function getPreparedFields(array $fields): array {
        $newFields['login'] = $fields['login'] ?: '';
        $newFields['password'] = $fields['password'] ?: '';
        foreach ($newFields as $key => $value) {
            if (!isset($fields[$key])) {
                unset($newFields[$key]);
            }
        }
        return $newFields;
    }

    protected function validateFields(array $fields = array(), ?array &$outputErrors = array()): bool {
        $fields = array(
                'login' => trim($fields['login']) ?: '',
                'password' => $fields['password'] ?: '',
        );

        $errors = array();
        if (empty($fields['login'])) {
            $errors[] = 'Введите логин!';
        }

        if (empty($fields['password'])) {
            $errors[] = 'Введите пароль!';
        }

        if (!empty($errors)) {
            $outputErrors = array_merge(
                    $errors,
                    $outputErrors ?: array()
            );
            return false;
        }
        return true;
    }
}
