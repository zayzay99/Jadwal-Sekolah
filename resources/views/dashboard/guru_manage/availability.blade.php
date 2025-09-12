@extends('dashboard.admin')
@section('content')
    <div class="content-header">
        <h2>Atur Ketersediaan Guru: {{ $guru->nama }}</h2>
    </div>

    <div class="form-container">
        <form action="{{ route('manage.guru.availability.update', $guru->id) }}" method="POST">
            @csrf
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Jam</th>
                            @foreach ($days as $day)
                                <th style="text-align: center">{{ $day }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($timeSlots as $slot)
                            <tr>
                                <td>{{ $slot->jam_mulai }} - {{ $slot->jam_selesai }}</td>
                                @foreach ($days as $day)
                                    <td style="text-align: center">
                                        <input type="checkbox" name="availability[{{ $day }}][]"
                                            value="{{ $slot->jam_mulai }} - {{ $slot->jam_selesai }}"
                                            @if (isset($availabilities[$day]) && in_array($slot->jam_mulai . ' - ' . $slot->jam_selesai, $availabilities[$day])) checked @endif>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Simpan Ketersediaan
                </button>
                <a href="{{ route('manage.guru.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>
@endsection
