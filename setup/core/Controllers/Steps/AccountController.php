<?php

namespace App\Controllers\Steps;

use App\Controllers\BaseController;
use App\Traits\LaravelBootstrap;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Laravel\Jetstream\Features;
use Illuminate\Database\QueryException;

class AccountController extends BaseController
{
    use LaravelBootstrap;

    public function __construct()
    {
        parent::__construct();
        $this->bootLaravel();
    }

    public function handle(string $lang, array $step)
    {
        if (!$this->checkAppKey()) {
            $this->redirectToPreviousStep($lang);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleFormSubmission($lang);
            // return;
        }

        $currentStep = $this->stepManager->getCurrentStep();
        $nextStep = $this->stepManager->getNextStep($currentStep, $lang);
        $prevStep = $this->stepManager->getPreviousStep($currentStep, $lang);

        return $this->render($step['template'], [
            'currentStep' => $currentStep,
            'nextStep' => $nextStep,
            'prevStep' => $prevStep,
            'usersTableExists' => Schema::hasTable('users'),
            'errors' => $this->getErrors()
        ]);
    }

    private function handleFormSubmission($lang): void
    {
        $errors = $this->validateForm();
        if (empty($errors)) {
            try {
                DB::beginTransaction();

                $userId = $this->createUser();
                $this->handleTeamCreation($userId);

                DB::commit();

                // Redirect to next step
                $currentStep = $this->stepManager->getCurrentStep();
                $nextStep = $this->stepManager->getNextStep($currentStep, $lang);
                header('Location: ' . htmlspecialchars($nextStep['url']));
                exit();
            } catch (QueryException $e) {
                DB::rollBack();

                // Check if the exception is due to a unique constraint violation
                if ($e->getCode() == 23000) {
                    $this->addError(t('create_account_email_already_exists', 'An account with this email already exists.'));
                } else {
                    $this->addError(t('create_account_registration_error', 'Registration failed: ') . $e->getMessage());
                }
            }
        } else {
            foreach ($errors as $error) {
                $this->addError($error);
            }
        }
    }

    private function validateForm(): array
    {
        $errors = [];
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($username)) {
            $errors[] = t('create_account_username_required', 'Username is required.');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = t('create_account_valid_email_required', 'Please enter a valid email address.');
        }
        if (empty($password)) {
            $errors[] = t('create_account_password_required', 'Password is required.');
        }
        if ($password !== $confirmPassword) {
            $errors[] = t('create_account_passwords_do_not_match', 'Passwords do not match.');
        }

        return $errors;
    }

    private function createUser(): int
    {
        return DB::table('users')->insertGetId([
            'name' => trim($_POST['username']),
            'email' => trim($_POST['email']),
            'password' => password_hash($_POST['password'], PASSWORD_BCRYPT),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    private function handleTeamCreation(int $userId): void
    {
        if (class_exists(Features::class) && Features::hasTeamFeatures()) {
            $teamId = DB::table('teams')->insertGetId([
                'name' => t('create_account_default_team_name', 'Main Team'),
                'user_id' => $userId,
                'personal_team' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::table('users')
                ->where('id', $userId)
                ->update(['current_team_id' => $teamId]);

            DB::table('team_user')->insert([
                'team_id' => $teamId,
                'user_id' => $userId,
                'role' => 'owner',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    private function checkAppKey(): bool
    {
        return !empty($_ENV['APP_KEY']);
    }

    private function redirectToPreviousStep($lang): void
    {
        $currentStep = $this->stepManager->getCurrentStep();
        $prevStep = $this->stepManager->getPreviousStep($currentStep, $lang);
        header('Location: ' . htmlspecialchars($prevStep['url']));
        exit();
    }
}
