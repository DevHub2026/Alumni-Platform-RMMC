<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Event;
use App\Models\Post;
use App\Models\Announcement;
use App\Models\EventRegistration;
use App\Models\AlumniProfile;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Alumni', User::where('role', 'alumni')->count())
                ->description('Registered alumni accounts')
                ->descriptionIcon('heroicon-m-users')
                ->color('blue'),

            Stat::make('Verified Alumni',
                User::where('role', 'alumni')
                    ->where('is_verified', true)
                    ->count()
            )
                ->description('Completed their profiles')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            Stat::make('Total Posts', Post::count())
                ->description(
                    Post::where('is_flagged', true)->count()
                    . ' flagged for review'
                )
                ->descriptionIcon('heroicon-m-document-text')
                ->color('warning'),

            Stat::make('Upcoming Events',
                Event::where('is_published', true)
                     ->where('event_date', '>=', now())
                     ->count()
            )
                ->description('Published and open')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),

            Stat::make('Event Registrations', EventRegistration::count())
                ->description('Total across all events')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('success'),

            Stat::make('Announcements',
                Announcement::where('is_published', true)->count()
            )
                ->description('Published announcements')
                ->descriptionIcon('heroicon-m-megaphone')
                ->color('blue'),
        ];
    }
}