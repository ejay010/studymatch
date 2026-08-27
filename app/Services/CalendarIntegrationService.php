<?php

namespace App\Services;

use Saloon\Http\Connector;
use Saloon\Http\Request;
use Saloon\Enums\Method;

/**
 * Saloon Connector for Google Calendar API
 * 
 * NOTE: Saloon is an object-oriented HTTP client wrapper for PHP. 
 * Instead of making raw Guzzle or Laravel Http calls everywhere, Saloon 
 * allows us to define "Connectors" (base URLs, default headers, auth) 
 * and "Requests" (specific endpoints). 
 */
class GoogleCalendarConnector extends Connector
{
    public function resolveBaseUrl(): string
    {
        return 'https://www.googleapis.com/calendar/v3';
    }

    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }
}

/**
 * Saloon Request for creating an event
 */
class CreateEventRequest extends Request
{
    protected Method $method = Method::POST;

    public function __construct(protected string $calendarId, protected array $eventData)
    {}

    public function resolveEndpoint(): string
    {
        return "/calendars/{$this->calendarId}/events";
    }

    protected function defaultBody(): array
    {
        // This is the JSON payload sent to the API
        return $this->eventData;
    }
}

class CalendarIntegrationService
{
    public function generateMeetingLink($educator, $booking)
    {
        // In a real scenario, we'd retrieve the educator's OAuth token here.
        // $connector = new GoogleCalendarConnector();
        // $connector->withTokenAuth($educator->google_token);

        // $request = new CreateEventRequest('primary', [
        //     'summary' => 'StudyMatch Session',
        //     'start' => ['dateTime' => $booking->starts_at->toRfc3339String()],
        //     'end' => ['dateTime' => $booking->ends_at->toRfc3339String()],
        // ]);
        
        // $response = $connector->send($request);
        // return $response->json('hangoutLink'); // Example response field
        
        return "https://meet.google.com/mock-link";
    }
}
