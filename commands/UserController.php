<?php

namespace app\commands;

use Yii;
use yii\helpers\Console;
use yii\console\Controller;
use app\models\User;

class UserController extends Controller
{
    private $newPassword = null;

    /**
     * @param string $username The name of user.
     * @param string $email The email of user.
     * @param string $password User's password. If it will not be not passed than random password will be generated.
     */
    public function actionCreateAdmin($username, $email, $password = null)
    {
        if (empty($password)) {
            $password = $this->generatePassword();
        }

        $this->newPassword = $password;

        $user = new User;
        $user->username = $username;
        $user->email = $email;
        $user->setPassword($password);
        $user->generateAuthKey();
        $user->status = User::STATUS_ACTIVE;
        $user->role = User::ROLE_ADMIN;
        $user->bio_name = 'Admin';
        $user->bio_surname = 'Admin';

        if (!$user->save()) {
            $this->outputErrors($user);
            return;
        }

        $this->stdout("User has been successfully created.\n", Console::FG_GREEN);
        $this->outputUser($user);
    }

    /**
     * @param string $usernameOrEmail The name of user or his email.
     * @param type $newPassword The new password. If it will not be not passed than random password will be generated.
     */
    public function actionChangePassword($usernameOrEmail, $newPassword = null)
    {
        $user = User::findByUsernameOrEmail($usernameOrEmail);

        if (!$user instanceof User) {
            $this->stdout("User with email or username '%s' was not found!\n", $usernameOrEmail, Console::FG_RED);
            return;
        }

        if (empty($newPassword)) {
            $newPassword = $this->generatePassword();
        }

        $this->newPassword = $newPassword;

        $user->setPassword($newPassword);

        if (!$user->save()) {
            $this->outputErrors($user);
            return;
        }

        $this->stdout("User's password has been successfully changed.\n", Console::FG_GREEN);
        $this->outputUser($user);
    }

    /**
     * @return string
     */
    protected function generatePassword()
    {
        return Yii::$app->getSecurity()->generateRandomString(8);
    }

    /**
     * @param User $user
     */
    protected function outputUser($user)
    {
        $this->stdout($user->getAttributeLabel('username') . ': ', Console::FG_YELLOW);
        $this->stdout("$user->username\n");
        $this->stdout($user->getAttributeLabel('email') . ': ', Console::FG_YELLOW);
        $this->stdout("$user->email\n");
        $this->stdout($user->getAttributeLabel('password') . ': ', Console::FG_YELLOW);
        $this->stdout("$this->newPassword\n");
    }

    /**
     * @param User $user
     */
    protected function outputErrors($user)
    {
        $this->stdout("Error was occured!\n", Console::FG_RED);
        foreach ($user->getErrors() as $field => $errors) {
            $this->stdout($user->getAttributeLabel($field) . ":\n");
            foreach ($errors as $error) {
                $this->stdout("$error\n", Console::FG_RED);
            }
        }
    }
}
