{{-- filepath: resources/views/jadwal/create.blade.php --}}
@extends('dashboard.admin')

@section('content')
<div class="content-header">
    <h2>Tambah Jadwal untuk Kelas {{ $kelas->nama_kelas }}</h2>
</div>

<div class="form-container">
    <form action="{{ route('jadwal.store') }}" method="POST">
        @csrf
        <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">

        <div class="form-group">
            <label for="guru_id">Guru</label>
            <select name="guru_id" id="guru_id" class="form-control" required>
                <option value="">-- Pilih Guru --</option>
                @foreach($gurus as $g)
                    <option value="{{ $g->id }}" data-pengampu="{{ $g->pengampu }}">{{ $g->nama }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="mapel">Mata Pelajaran</label>
            <input type="text" name="mapel" id="mapel" class="form-control" placeholder="Mata pelajaran akan terisi otomatis" required readonly>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="hari">Hari</label>
                <select name="hari" id="hari" class="form-control" required>
                    <option value="">-- Pilih Hari --</option>
                    <option value="Senin">Senin</option>
                    <option value="Selasa">Selasa</option>
                    <option value="Rabu">Rabu</option>
                    <option value="Kamis">Kamis</option>
                    <option value="Jumat">Jumat</option>
                    <option value="Sabtu">Sabtu</option>
                </select>
            </div>

            <div class="form-group">
            <label>Jam Pelajaran</label>
            <div class="analog-clock-container">
                <input type="hidden" name="jam" id="jam_hidden" required>
                <button type="button" class="clock-picker-btn" id="clockPickerBtn">
                    <i class="fas fa-clock"></i> Set Waktu Pelajaran
                </button>
                <div id="selectedClock" class="selected-clock" style="display: none;">
                    <div class="time-display">
                        <span class="start-time"></span> - <span class="end-time"></span>
                    </div>
                    <button type="button" class="btn-change" id="changeClockBtn">Ubah</button>
                </div>
            </div>
        </div>
    </div>

        <div class="form-actions">
            <button type="button" class="btn btn-secondary" onclick="window.history.back()">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan Jadwal</button>
        </div>
    </form>
</div>

<!-- Analog Clock Modal -->
<div id="clockPickerModal" class="clock-picker-modal" style="display: none;">
        <div class="clock-picker-content">
            <div class="clock-picker-header">
                <h5>Set Waktu Pelajaran</h5>
                <button type="button" class="close-btn" id="closeClockPickerBtn">&times;</button>
            </div>
            <div class="clock-picker-body">
                <div class="time-displays-section">
                    <div class="time-display-group">
                        <label>Jam Mulai:</label>
                        <input type="text" class="time-badge" id="startTimeDisplay" 
                               value="07:00" placeholder="HH:MM" maxlength="5">
                        <div class="error-message" id="startTimeError" style="display: none;"></div>
                    </div>
                    <div class="time-display-group">
                        <label>Jam Selesai:</label>
                        <input type="text" class="time-badge" id="endTimeDisplay" 
                               value="07:45" placeholder="HH:MM" maxlength="5">
                        <div class="error-message" id="endTimeError" style="display: none;"></div>
                    </div>
                </div>

                <div class="clocks-container">
                    <div class="clock-wrapper">
                        <h6>Jam Mulai</h6>
                        <div class="clock-circle" id="startClock">
                            <div class="clock-face">
                                <!-- Numbers positioned properly -->
                                <div class="number number-12">12</div>
                                <div class="number number-1">1</div>
                                <div class="number number-2">2</div>
                                <div class="number number-3">3</div>
                                <div class="number number-4">4</div>
                                <div class="number number-5">5</div>
                                <div class="number number-6">6</div>
                                <div class="number number-7">7</div>
                                <div class="number number-8">8</div>
                                <div class="number number-9">9</div>
                                <div class="number number-10">10</div>
                                <div class="number number-11">11</div>
                                <!-- Hands -->
                                <div class="hand hour-hand" id="startHourHand"></div>
                                <div class="hand minute-hand" id="startMinuteHand"></div>
                                <div class="center-dot"></div>
                            </div>
                        </div>
                    </div>

                    <div class="clock-wrapper">
                        <h6>Jam Selesai</h6>
                        <div class="clock-circle" id="endClock">
                            <div class="clock-face">
                                <div class="number number-12">12</div>
                                <div class="number number-1">1</div>
                                <div class="number number-2">2</div>
                                <div class="number number-3">3</div>
                                <div class="number number-4">4</div>
                                <div class="number number-5">5</div>
                                <div class="number number-6">6</div>
                                <div class="number number-7">7</div>
                                <div class="number number-8">8</div>
                                <div class="number number-9">9</div>
                                <div class="number number-10">10</div>
                                <div class="number number-11">11</div>
                                <div class="hand hour-hand" id="endHourHand"></div>
                                <div class="hand minute-hand" id="endMinuteHand"></div>
                                <div class="center-dot"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" id="cancelClockBtn">BATAL</button>
                    <button type="button" class="btn-confirm" id="setClockBtn">SET WAKTU</button>
                </div>
            </div>
        </div>
    </div>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: #2d6a4f; 
            font-size: 16px;
        }

        /* Clock Picker Button */
        .analog-clock-container {
            position: relative;
        }

        .clock-picker-btn {
            width: 100%;
            padding: 15px 20px;
            text-align: left;
            border: 2px solid #2d6a4f;
            border-radius: 12px;
            background: linear-gradient(135deg, #ffffff, #f8fff9);
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 16px;
            font-weight: 500;
            color: #2d6a4f;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 2px 8px rgba(45, 106, 79, 0.1);
        }

        .clock-picker-btn:hover {
            background: linear-gradient(135deg, #f8fff9, #e6f0fa);
            border-color: #1c3d2e;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(45, 106, 79, 0.2);
        }

        .clock-picker-btn:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(45, 106, 79, 0.15);
        }

        .clock-picker-btn i {
            font-size: 20px;
            color: #2d6a4f;
        }

        .selected-clock {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 20px;
            border: 2px solid #28a745;
            border-radius: 12px;
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            margin-top: 10px;
            box-shadow: 0 2px 8px rgba(40, 167, 69, 0.15);
        }

        .time-display {
            font-weight: 600;
            color: #155724;
            font-size: 18px;
            letter-spacing: 0.5px;
        }

        .btn-change {
            background: linear-gradient(135deg, #6c757d, #495057);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-change:hover {
            background: linear-gradient(135deg, #5a6268, #404649);
            transform: translateY(-1px);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        }

        /* Modal Styles */
        .clock-picker-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .clock-picker-content {
            background: white;
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
            max-width: 900px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }

        .clock-picker-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 25px 30px;
            border-bottom: 1px solid #eee;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 20px 20px 0 0;
        }

        .clock-picker-header h5 {
            font-size: 20px;
            font-weight: 600;
            color: #2d6a4f;
            margin: 0;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 28px;
            cursor: pointer;
            color: #999;
            padding: 0;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .close-btn:hover {
            background-color: #ff4757;
            color: white;
            transform: scale(1.1);
        }

        .clock-picker-body {
            padding: 40px 30px;
        }

        .time-displays-section {
            display: flex;
            justify-content: center;
            gap: 80px;
            margin-bottom: 40px;
        }

        .time-display-group {
            text-align: center;
        }

        .time-display-group label {
            display: block;
            margin-bottom: 12px;
            font-weight: 600;
            color: #666;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Editable Time Badge - FITUR BARU */
        .time-badge {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 8px 16px;
            border-radius: 25px;
            font-size: 20px;
            font-weight: 600;
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3);
            min-width: 90px;
            letter-spacing: 1px;
            border: none;
            text-align: center;
            cursor: text;
            transition: all 0.3s ease;
            outline: none;
        }

        .time-badge:hover {
            box-shadow: 0 6px 25px rgba(102, 126, 234, 0.4);
            transform: translateY(-2px);
        }

        .time-badge:focus {
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.3);
            transform: translateY(-2px);
        }

        .time-badge::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .clocks-container {
            display: flex;
            justify-content: center;
            gap: 80px;
            margin-bottom: 40px;
        }

        .clock-wrapper {
            text-align: center;
        }

        .clock-wrapper h6 {
            margin-bottom: 20px;
            color: #2d6a4f;
            font-weight: 600;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Clock Circle */
        .clock-circle {
            width: 200px;
            height: 200px;
            position: relative;
            margin: 0 auto;
        }

        .clock-face {
            width: 100%;
            height: 100%;
            border: 5px solid #2d6a4f;
            border-radius: 50%;
            position: relative;
            background: radial-gradient(circle, #ffffff 60%, #f8f9fa 100%);
            box-shadow: 
                0 8px 25px rgba(0,0,0,0.15),
                inset 0 2px 8px rgba(255,255,255,0.9),
                inset 0 -2px 8px rgba(0,0,0,0.05);
        }

        /* Numbers */
        .number {
            position: absolute;
            font-size: 16px;
            font-weight: 700;
            color: #2d6a4f;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .number-12 { top: 8px; left: 50%; transform: translateX(-50%); }
        .number-1 { top: 25px; right: 32px; }
        .number-2 { top: 50px; right: 12px; }
        .number-3 { top: 50%; right: 5px; transform: translateY(-50%); }
        .number-4 { bottom: 50px; right: 12px; }
        .number-5 { bottom: 25px; right: 32px; }
        .number-6 { bottom: 8px; left: 50%; transform: translateX(-50%); }
        .number-7 { bottom: 25px; left: 32px; }
        .number-8 { bottom: 50px; left: 12px; }
        .number-9 { top: 50%; left: 5px; transform: translateY(-50%); }
        .number-10 { top: 50px; left: 12px; }
        .number-11 { top: 25px; left: 32px; }

        /* Clock Hands */
        .hand {
            position: absolute;
            border-radius: 4px;
            transform-origin: bottom center;
            left: 50%;
            bottom: 50%;
            cursor: grab;
            transition: all 0.1s ease;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
        }

        .hand:active {
            cursor: grabbing;
            transform: scale(1.05);
        }

        .hand:hover {
            filter: brightness(1.1);
        }

        .hour-hand {
            width: 6px;
            height: 55px;
            margin-left: -3px;
            background: linear-gradient(to top, #1e293b, #475569);
            z-index: 3;
        }

        .minute-hand {
            width: 4px;
            height: 75px;
            margin-left: -2px;
            background: linear-gradient(to top, #ef4444, #dc2626);
            z-index: 2;
        }

        .center-dot {
            position: absolute;
            width: 14px;
            height: 14px;
            background: radial-gradient(circle, #1e293b, #0f172a);
            border-radius: 50%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 4;
            box-shadow: 0 0 8px rgba(0,0,0,0.4);
            border: 2px solid white;
        }

        /* Modal Action Buttons */
        .modal-actions {
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .btn-cancel, .btn-confirm {
            padding: 14px 32px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-cancel {
            background: linear-gradient(135deg, #6b7280, #4b5563);
            color: white;
        }

        .btn-cancel:hover {
            background: linear-gradient(135deg, #4b5563, #374151);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .btn-confirm {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        .btn-confirm:hover {
            background: linear-gradient(135deg, #059669, #047857);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        /* Validation Error Styles */
        .time-badge.error {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .error-message {
            color: #dc2626;
            font-size: 12px;
            margin-top: 5px;
            text-align: center;
            font-weight: 500;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .clocks-container {
                flex-direction: column;
                gap: 30px;
            }
            
            .time-displays-section {
                flex-direction: column;
                gap: 20px;
            }
            
            .clock-picker-content {
                width: 95%;
                margin: 10px;
            }

            .clock-picker-body {
                padding: 25px 20px;
            }
            
            .clock-circle {
                width: 170px;
                height: 170px;
            }
            
            .hour-hand {
                height: 45px;
            }
            
            .minute-hand {
                height: 62px;
            }
            
            .number {
                font-size: 14px;
                width: 24px;
                height: 24px;
            }

            .clock-picker-btn {
                padding: 12px 16px;
                font-size: 14px;
            }

            .time-badge {
                font-size: 18px;
                padding: 10px 20px;
            }
        }
    </style>
<script>

         // Fungsi untuk mengisi mata pelajaran otomatis
    document.getElementById('guru_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const pengampu = selectedOption.getAttribute('data-pengampu');
        
        if (pengampu) {
            document.getElementById('mapel').value = pengampu;
        } else {
            document.getElementById('mapel').value = '';
        }
    });

        // Clock Picker functionality
        const clockPickerBtn = document.getElementById('clockPickerBtn');
        const changeClockBtn = document.getElementById('changeClockBtn');
        const clockPickerModal = document.getElementById('clockPickerModal');
        const closeClockPickerBtn = document.getElementById('closeClockPickerBtn');
        const cancelClockBtn = document.getElementById('cancelClockBtn');
        const setClockBtn = document.getElementById('setClockBtn');
        const selectedClockDiv = document.getElementById('selectedClock');
        const jamHidden = document.getElementById('jam_hidden');

        // Clock hands
        const startHourHand = document.getElementById('startHourHand');
        const startMinuteHand = document.getElementById('startMinuteHand');
        const endHourHand = document.getElementById('endHourHand');
        const endMinuteHand = document.getElementById('endMinuteHand');

        // Editable time displays
        const startTimeDisplay = document.getElementById('startTimeDisplay');
        const endTimeDisplay = document.getElementById('endTimeDisplay');
        const startTimeError = document.getElementById('startTimeError');
        const endTimeError = document.getElementById('endTimeError');

        // Time values
        let startTime = { hour: 7, minute: 0 };
        let endTime = { hour: 7, minute: 45 };

        // Validasi format waktu
        function validateTimeFormat(timeString) {
            const timeRegex = /^([0-1]?[0-9]|2[0-3]):([0-5][0-9])$/;
            return timeRegex.test(timeString);
        }

        // Konversi string waktu ke objek time
        function parseTimeString(timeString) {
            if (!validateTimeFormat(timeString)) return null;
            const [hour, minute] = timeString.split(':').map(Number);
            return { hour, minute };
        }

        // Format waktu ke string
        function formatTime(time) {
            return `${time.hour.toString().padStart(2, '0')}:${time.minute.toString().padStart(2, '0')}`;
        }

        // Update jam analog berdasarkan input digital
        function updateClockFromInput(type, timeString) {
            const parsedTime = parseTimeString(timeString);
            const errorElement = type === 'start' ? startTimeError : endTimeError;
            const inputElement = type === 'start' ? startTimeDisplay : endTimeDisplay;

            if (!parsedTime) {
                inputElement.classList.add('error');
                errorElement.textContent = 'Format tidak valid (gunakan HH:MM)';
                errorElement.style.display = 'block';
                return false;
            }

            if (parsedTime.hour < 6 || parsedTime.hour > 18) {
                inputElement.classList.add('error');
                errorElement.textContent = 'Jam harus antara 06:00 - 18:00';
                errorElement.style.display = 'block';
                return false;
            }

            // Reset error state
            inputElement.classList.remove('error');
            errorElement.style.display = 'none';

            // Update time object
            if (type === 'start') {
                startTime = parsedTime;
            } else {
                endTime = parsedTime;
            }

            updateClock(type);
            return true;
        }

        function initializeClocks() {
            updateClock('start');
            updateClock('end');
        }

        function updateClock(type) {
            const time = type === 'start' ? startTime : endTime;
            const hourHand = type === 'start' ? startHourHand : endHourHand;
            const minuteHand = type === 'start' ? startMinuteHand : endMinuteHand;
            const display = type === 'start' ? startTimeDisplay : endTimeDisplay;

            const hourAngle = (time.hour % 12) * 30 + (time.minute * 0.5);
            const minuteAngle = time.minute * 6;

            hourHand.style.transform = `rotate(${hourAngle}deg)`;
            minuteHand.style.transform = `rotate(${minuteAngle}deg)`;

            const formattedTime = formatTime(time);
            display.value = formattedTime;
        }

        function getAngleFromMouse(event, clockElement) {
            const rect = clockElement.getBoundingClientRect();
            const centerX = rect.left + rect.width / 2;
            const centerY = rect.top + rect.height / 2;
            const mouseX = event.clientX;
            const mouseY = event.clientY;
            const angle = Math.atan2(mouseY - centerY, mouseX - centerX);
            return (angle * 180 / Math.PI + 90 + 360) % 360;
        }

        function setupDraggableHands() {
            const clocks = ['start', 'end'];
            clocks.forEach(clockType => {
                const clockElement = document.getElementById(clockType + 'Clock');
                const hourHand = document.getElementById(clockType + 'HourHand');
                const minuteHand = document.getElementById(clockType + 'MinuteHand');
                let isDragging = false;
                let currentHand = null;

                function startDrag(hand, e) {
                    isDragging = true;
                    currentHand = hand;
                    e.preventDefault();
                }

                function drag(e) {
                    if (!isDragging || !currentHand) return;
                    const angle = getAngleFromMouse(e, clockElement);
                    const time = clockType === 'start' ? startTime : endTime;

                    if (currentHand === 'hour') {
                        let hour = Math.round(angle / 30);
                        if (hour === 0) hour = 12;
                        time.hour = hour;
                    } else if (currentHand === 'minute') {
                        time.minute = Math.round(angle / 6) % 60;
                    }
                    updateClock(clockType);
                    
                    // Clear any previous errors when dragging
                    const errorElement = clockType === 'start' ? startTimeError : endTimeError;
                    const inputElement = clockType === 'start' ? startTimeDisplay : endTimeDisplay;
                    inputElement.classList.remove('error');
                    errorElement.style.display = 'none';
                }

                function stopDrag() {
                    isDragging = false;
                    currentHand = null;
                }

                // Mouse events
                hourHand.addEventListener('mousedown', (e) => startDrag('hour', e));
                minuteHand.addEventListener('mousedown', (e) => startDrag('minute', e));

                document.addEventListener('mousemove', drag);
                document.addEventListener('mouseup', stopDrag);

                // Touch events
                hourHand.addEventListener('touchstart', (e) => startDrag('hour', e));
                minuteHand.addEventListener('touchstart', (e) => startDrag('minute', e));

                document.addEventListener('touchmove', (e) => drag(e.touches[0]));
                document.addEventListener('touchend', stopDrag);
            });
        }

        // Event listeners untuk input editable
        startTimeDisplay.addEventListener('input', function(e) {
            updateClockFromInput('start', e.target.value);
        });

        endTimeDisplay.addEventListener('input', function(e) {
            updateClockFromInput('end', e.target.value);
        });

        // Auto-format saat mengetik
        function autoFormatTime(input) {
            let value = input.value.replace(/[^\d]/g, ''); // Hapus non-digit
            
            if (value.length >= 2) {
                value = value.substring(0, 2) + ':' + value.substring(2, 4);
            }
            
            input.value = value;
        }

        startTimeDisplay.addEventListener('keyup', function(e) {
            autoFormatTime(e.target);
            if (e.target.value.length === 5) {
                updateClockFromInput('start', e.target.value);
            }
        });

        endTimeDisplay.addEventListener('keyup', function(e) {
            autoFormatTime(e.target);
            if (e.target.value.length === 5) {
                updateClockFromInput('end', e.target.value);
            }
        });

        // Validasi saat kehilangan fokus
        startTimeDisplay.addEventListener('blur', function(e) {
            if (e.target.value) {
                updateClockFromInput('start', e.target.value);
            }
        });

        endTimeDisplay.addEventListener('blur', function(e) {
            if (e.target.value) {
                updateClockFromInput('end', e.target.value);
            }
        });

        function openClockPicker() {
            clockPickerModal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            initializeClocks();
            setupDraggableHands();
        }

        function closeClockPicker() {
            clockPickerModal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        function validateTimeRange() {
            const startMinutes = startTime.hour * 60 + startTime.minute;
            const endMinutes = endTime.hour * 60 + endTime.minute;

            if (endMinutes <= startMinutes) {
                endTimeDisplay.classList.add('error');
                endTimeError.textContent = 'Jam selesai harus lebih besar dari jam mulai';
                endTimeError.style.display = 'block';
                return false;
            }

            if (endMinutes - startMinutes < 30) {
                endTimeDisplay.classList.add('error');
                endTimeError.textContent = 'Durasi minimal 30 menit';
                endTimeError.style.display = 'block';
                return false;
            }

            // Reset error states
            startTimeDisplay.classList.remove('error');
            endTimeDisplay.classList.remove('error');
            startTimeError.style.display = 'none';
            endTimeError.style.display = 'none';
            return true;
        }

        function setTime() {
            // Validasi input terlebih dahulu
            const startValid = updateClockFromInput('start', startTimeDisplay.value);
            const endValid = updateClockFromInput('end', endTimeDisplay.value);
            
            if (!startValid || !endValid) {
                return; // Jangan tutup modal jika ada error
            }

            if (!validateTimeRange()) {
                return; // Jangan tutup modal jika validasi range gagal
            }

            const startStr = formatTime(startTime);
            const endStr = formatTime(endTime);
            jamHidden.value = `${startStr}-${endStr}`;

            selectedClockDiv.querySelector('.start-time').textContent = startStr;
            selectedClockDiv.querySelector('.end-time').textContent = endStr;

            clockPickerBtn.style.display = 'none';
            selectedClockDiv.style.display = 'flex';

            closeClockPicker();
        }

        // Event listeners
        clockPickerBtn.addEventListener('click', openClockPicker);
        changeClockBtn.addEventListener('click', openClockPicker);
        closeClockPickerBtn.addEventListener('click', closeClockPicker);
        cancelClockBtn.addEventListener('click', closeClockPicker);
        setClockBtn.addEventListener('click', setTime);

        clockPickerModal.addEventListener('click', function(e) {
            if (e.target === clockPickerModal) closeClockPicker();
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && clockPickerModal.style.display === 'flex') {
                closeClockPicker();
            }
        });

        // Initialize on load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Editable clock picker initialized');
            initializeClocks();
        });


    </script>
@endsection