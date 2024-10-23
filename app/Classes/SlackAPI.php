<?php

namespace App\Classes;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;
use Sentry\Laravel\Facade as Sentry;
use Sentry\State\Scope;

/**
 * Create Slack notification helper
 */
class SlackAPI
{
    protected $client;

    public function __construct($token)
    {
        if ($token) {
            $this->client = new Client([
                'base_uri' => 'https://slack.com/api/',
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-type' => 'application/json',
                    'Authorization' => "Bearer {$token}"
                ]
            ]);
        }
    }

    public function send($channel, $message)
    {
        if ($this->client && $id = $this->getChannelID($channel)) {
            try {
                $response = $this->client->post('chat.postMessage', [
                    RequestOptions::JSON => ['channel' => $id, 'text' => $message]
                ]);
                $response = json_decode($response->getBody());
                if ($response->ok) {
                    return true;
                }
            } catch (\Throwable $th) {
                Sentry::captureException($th);
                Sentry::configureScope(function (Scope $scope) {
                    $scope->setTag('feature', 'slack');
                });
            }
        }

        return false;
    }

    public function sendWithContext($channel, $message, $context)
    {
        if ($this->client && $id = $this->getChannelID($channel)) {
            try {
                $blocks[] = [
                    'type' => 'section',
                    'text' => [
                        "type" => "plain_text",
                        "text" => $message,
                        "emoji" => true
                    ]
                ];

                if ($json = json_encode($context, JSON_PRETTY_PRINT)) {
                    $blocks[] = [
                        'type' => 'section',
                        'text' => [
                            "type" => "mrkdwn",
                            "text" => "```$json```"
                        ]
                    ];
                }

                $response = $this->client->post('chat.postMessage', [
                    RequestOptions::JSON => ['channel' => $id, 'blocks' => $blocks]
                ]);
                $response = json_decode($response->getBody());
                if ($response->ok) {
                    return true;
                }
            } catch (\Throwable $th) {
                Sentry::captureException($th);
                Sentry::configureScope(function (Scope $scope) {
                    $scope->setTag('feature', 'slack');
                });
            }
        }

        return false;
    }

    protected function getChannelID($channel)
    {
        if (Str::startsWith($channel, '#')) {
            $channel = ltrim($channel, '#');
            try {
                foreach ($this->getChannels() as $c) {
                    if (Str::lower($c->name) === Str::lower($channel)) {
                        return $c->id;
                    }
                }
            } catch (\Throwable $th) {
                Sentry::captureException($th);
                Sentry::configureScope(function (Scope $scope) {
                    $scope->setTag('feature', 'slack');
                });
            }
            $this->send('#developers', "Channel $channel not found make sure the Printenbind app is added to the channel");
        } elseif (Str::startsWith($channel, '@')) {
            $channel = ltrim($channel, '@');
            try {
                foreach ($this->getUsers() as $user) {
                    if (Str::lower($user->name) === Str::lower($channel)) {
                        return $user->id;
                    }
                }
            } catch (\Throwable $th) {
                Sentry::captureException($th);
                Sentry::configureScope(function (Scope $scope) {
                    $scope->setTag('feature', 'slack');
                });
            }
            $this->send('#developers', "User $channel not found to send a message to, make sure the username matches");
        } else {
            throw new \Exception('Please send a message to a channel starting with # or a user starting with @');
        }

        return false;
    }

    public function getUsers()
    {
        if ($this->client) {
            return Cache::remember("slack_users_list", Date::now()->addHours(1), function () {
                $data = collect([]);
                $response = $this->client->get('users.list');
                $response = json_decode($response->getBody());

                if ($response->ok) {
                    $users = $response->members;
                    foreach ($users as $value) {
                        if ($value->is_bot === true || $value->deleted === true) {
                            continue;
                        }
                        $data->push($value);
                    }

                    return $data;
                } else {
                    throw new \Exception('Cannot fetch users from Slack');
                }
            });
        }

        return false;
    }

    public function getChannels()
    {
        if ($this->client) {
            return Cache::remember("slack_channels_list", Date::now()->addHours(1), function () {
                $data = collect([]);
                $response = $this->client->get('conversations.list', [
                    'query' => ['types' => 'public_channel,private_channel']
                ]);
                $response = json_decode($response->getBody());

                if ($response->ok) {
                    $channels = $response->channels;
                    foreach ($channels as $value) {
                        if ($value->is_archived === true) {
                            continue;
                        }
                        $data->push($value);
                    }

                    return $data;
                } else {
                    throw new \Exception('Cannot fetch channels from Slack');
                }
            });
        }

        return false;
    }
}
