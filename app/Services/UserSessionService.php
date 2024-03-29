<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;
use stdClass;

class UserSessionService
{
    public function getProperties(): Collection
    {
        if ($this->isNotUsingDatabaseTable()) {
            return collect();
        }

        return $this->getUserSessions()->map(fn($session) => (object) [
            'agent' => $this->createAgent($session),
            'ip_address' => $session->ip_address,
            'is_current_device' => $session->id === request()->session()->getId(),
            'last_active' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
        ]);
    }

    private function isNotUsingDatabaseTable(): bool
    {
        return config('session.driver') !== 'database';
    }

    private function getUserSessions(): Collection
    {
        return DB::connection(config('session.connection'))->table($this->sessionDatabaseTable())
            ->where('user_id', Auth::user()->getAuthIdentifier())
            ->orderBy('last_activity', 'desc')
            ->get();
    }

    private function sessionDatabaseTable(): string
    {
        return config('session.table', 'sessions');
    }

    private function createAgent(stdClass $session): Agent
    {
        return tap(new Agent, static function (Agent $agent) use ($session) {
            $agent->setUserAgent($session->user_agent);
        });
    }

    public function canLogoutOtherSession(): bool
    {
        return $this->otherSessions()->count() > 0;
    }

    public function otherSessions(): Builder
    {
        return DB::connection(config('session.connection'))->table($this->sessionDatabaseTable())
            ->where('user_id', Auth::user()->getAuthIdentifier())
            ->where('id', '!=', request()->session()->getId());
    }

    /**
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function logoutOtherBrowserSessions(string $password): void
    {
        if ($this->sessionDatabaseTable() && ! $this->canLogoutOtherSession()) {
            return;
        }

        auth()->logoutOtherDevices($password);

        $this->otherSessions()->delete();
    }
}
