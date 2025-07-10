@php
    $activityId = $get('activity_id');
    $activity = $activityId ? \App\Models\Activity::find($activityId) : null;
    $calendar = $activity?->activityCalendars()->orderByDesc('start_date')->first();
@endphp
@if($calendar)
    <div class="mb-4 p-4 bg-gray-50 rounded">
        <div class="flex flex-col md:flex-row md:space-x-8">
            <div>
                <span class="font-semibold">Fecha de inicio:</span>
                <span>{{ $calendar->start_date }}</span>
            </div>
            <div>
                <span class="font-semibold">Fecha de fin:</span>
                <span>{{ $calendar->end_date }}</span>
            </div>
        </div>
    </div>
@endif
