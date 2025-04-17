@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>{{ __('printers.history.title') }}</h1>

        {{ $printJobs->links() }}
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th class="text-end table-id">ID</th>
                    <th>{{ __('printer.history.status') }}</th>
                    <th>{{ __('printer.history.printer') }}</th>
                    <th>{{ __('printer.history.task') }}</th>
                    <th>{{ __('printer.history.filament') }}</th>
                    <th>{{ __('printer.history.date') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($printJobs as $printJob)
                    <tr>
                        <td class="text-end table-id">{{ $printJob->id }}</td>
                        <td>{{ $printJob->status->label() }}</td>
                        <td>{{ $printJob->printer->name }}</td>
                        <td>
                            @if($printJob->partTasks->count() > 0)
                                <div>
                                    @php
                                        $groupedTasks = $printJob->partTasks->groupBy(function($task) {
                                            return $task->task->name;
                                        });
                                    @endphp

                                    @foreach($groupedTasks as $taskName => $tasks)
                                        <div class="mb-1">{{ $taskName }}</div>
                                        <ul class="list-unstyled ms-3 mb-2">
                                            @foreach($tasks as $partTask)
                                                <li><b>x{{ $partTask->pivot->count_printed }}</b> {{ $partTask->part->name }}</li>
                                            @endforeach
                                        </ul>
                                    @endforeach
                                </div>
                            @else
                                {{ $printJob->filename }}
                            @endif
                        </td>
                        <td>
                            @if($printJob->spools)
                                @foreach($printJob->spools as $filamentSpool)
                                    <div class="mb-1">
                                        <b><x-number :value="$filamentSpool->weight_used" precision="4" noEmpty /></b>
                                        @foreach($filamentSpool->filament->colors as $color)
                                            <span style="background-color: {{ $color }}; width: 20px; height: 20px; display: inline-block;"></span>
                                        @endforeach
                                        <span class="small text-muted">#{{ $filamentSpool->id }}</span>
                                        {{ $filamentSpool->filament->name }} {{ $filamentSpool->filament->type->name }}, {{ $filamentSpool->filament->vendor->name }}, {{ $filamentSpool->packaging->name }}
                                    </div>
                                    <ul class="list-unstyled ms-3 mb-2">

                                    </ul>

                                @endforeach
                            @else
                                {{ __('printer.history.no_filament') }}
                            @endif
                        </td>
                        <td>{{ $printJob->end_time?->format('Y-m-d H:i:s') }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        {{ $printJobs->links() }}
    </div>
@endsection
