<?php

namespace App\Services\Chat;

use App\Models\ChatAvailability;
use App\Models\ChatClosedDate;
use Carbon\Carbon;

/**
 * Bepaalt of de live chat open is.
 *
 * Alles in Europe/Amsterdam (app-timezone is UTC — zelfde valkuil als coupons).
 */
class ChatAvailabilityService
{
    public const TIMEZONE = 'Europe/Amsterdam';

    /**
     * @return array{open: bool, reason: string|null, opens_at: string|null}
     */
    public function status(?Carbon $now = null): array
    {
        $now = ($now ?? now())->copy()->setTimezone(self::TIMEZONE);
        $today = $now->toDateString();

        $closed = ChatClosedDate::whereDate('closed_at', $today)->first();
        if ($closed) {
            return [
                'open' => false,
                'reason' => $closed->reason ?: 'Wegens een feestdag zijn wij vandaag gesloten.',
                'opens_at' => $this->nextOpening($now)->toIso8601String(),
            ];
        }

        $row = ChatAvailability::where('day_of_week', (int) $now->dayOfWeek)->first();

        if (! $row || ! $row->is_open || ! $row->open_at || ! $row->close_at) {
            return [
                'open' => false,
                'reason' => 'Wij zijn nu gesloten.',
                'opens_at' => $this->nextOpening($now)->toIso8601String(),
            ];
        }

        $open = Carbon::parse($row->open_at, self::TIMEZONE)->setDateFrom($now);
        $close = Carbon::parse($row->close_at, self::TIMEZONE)->setDateFrom($now);

        if ($now->gte($open) && $now->lt($close)) {
            return ['open' => true, 'reason' => null, 'opens_at' => null];
        }

        return [
            'open' => false,
            'reason' => $now->lt($open)
                ? 'Wij zijn nu gesloten. Wij zijn open vanaf '.$open->format('H:i').'.'
                : 'Wij zijn nu gesloten.',
            'opens_at' => $this->nextOpening($now)->toIso8601String(),
        ];
    }

    public function isOpen(?Carbon $now = null): bool
    {
        return $this->status($now)['open'];
    }

    /**
     * Eerstvolgende openingsmoment (max 14 dagen vooruit kijken).
     */
    protected function nextOpening(Carbon $now): Carbon
    {
        for ($i = 0; $i < 14; $i++) {
            $day = $now->copy()->addDays($i)->startOfDay();
            $dateStr = $day->toDateString();

            if (ChatClosedDate::whereDate('closed_at', $dateStr)->exists()) {
                continue;
            }

            $row = ChatAvailability::where('day_of_week', (int) $day->dayOfWeek)->first();
            if (! $row || ! $row->is_open || ! $row->open_at) {
                continue;
            }

            $candidate = Carbon::parse($row->open_at, self::TIMEZONE)->setDateFrom($day);

            if ($candidate->gt($now)) {
                return $candidate;
            }
        }

        return $now->copy()->addDay();
    }
}
