<?php

namespace App\Actions\Users;

use App\Models\User;
use App\Notifications\Users\AccountDeactivatedNotification;
use Illuminate\Support\Facades\DB;
use Spatie\DataTransferObject\DataTransferObject;

/**
 * Class DeactivateAction
 *
 * @package App\Actions\Users
 */
class DeactivateAction
{
    /**
     * Method for processing the user deactivation in the application.
     *
     * @param  User               $user               The storage entity from the given user.
     * @param  DataTransferObject $dataTransferObject The information object that needs to be stored.
     * @return void
     */
    public function execute(User $user, DataTransferObject $dataTransferObject): void
    {
        $authUser = auth()->user();

        DB::transaction(function () use ($user, $authUser, $dataTransferObject): void {
            $authUser->logActivity('Gebruikers', __('Heeft het account van :user gedeactiveerd in de applicatie', ['user' => $user->name]));

            $user->ban($dataTransferObject->toArray());
            $user->notify(new AccountDeactivatedNotification($dataTransferObject->comment));
        });
    }
}