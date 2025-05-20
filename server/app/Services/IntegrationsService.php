<?php

namespace App\Services;

use App\Models\Integration;
use Illuminate\Support\Facades\Schema;

class IntegrationsService
{
    public function getUserIntegrations($user)
    {
        $integrations = $user->integrations()->with('credentials')->get();
        $tableColumns = Schema::getColumnListing('integrations');
        if (!in_array('status', $tableColumns))
            foreach ($integrations as $integration)
                $integration->status = 'active';

        return $integrations;
    }

    public function getProviders()
    {
        return [
            [
                'id' => 'slack',
                'name' => 'Slack',
                'type' => 'workspace',
                'description' => 'Slack workspace integration',
                'iconUrl' => '/assets/images/w_slack_icon.png',
            ],
            [
                'id' => 'notion',
                'name' => 'Notion',
                'type' => 'channel',
                'description' => 'Notion workspace integration',
                'iconUrl' => '/assets/images/notion_icon.png',
            ],
            [
                'id' => 'calendar',
                'name' => 'Google Calendar',
                'type' => 'scheduler',
                'description' => 'Google Calendar integration',
                'iconUrl' => '/assets/images/calendar_icon.png',
            ],
            [
                'id' => 'email',
                'name' => 'Email',
                'type' => 'Email',
                'description' => 'Email integration',
                'iconUrl' => '/assets/images/w_mail_icon.png',
            ],
        ];
    }

    public function getConnectUrl($providerId)
    {
        if ($providerId === 'notion')
            return '/api/v1/integrations/notion/redirect';

        return '/api/v1/integrations/' . $providerId . '/oauth';
    }

    public function updateStatus($user, $integrationId, $status)
    {
        $integration = Integration::where('id', $integrationId)
            ->where('user_id', $user->id)
            ->first();
        if (!$integration)
            return false;

        $tableColumns = Schema::getColumnListing('integrations');
        if (in_array('status', $tableColumns)) {
            $integration->status = $status;
            $integration->save();
        }
        return true;
    }

    public function disconnect($user, $integrationId)
    {
        $integration = Integration::where('id', $integrationId)
            ->where('user_id', $user->id)
            ->first();
        if (!$integration)
            return false;

        $integration->status = 'inactive';
        $integration->save();
        return true;
    }
}
