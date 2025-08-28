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
                @foreach($guru as $g)
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
                    <button type="button" class="btn btn-outline-primary clock-picker-btn" id="clockPickerBtn">
                        <i class="fas fa-clock"></i> Set Waktu Pelajaran
                    </button>
                    <div id="selectedClock" class="selected-clock" style="display: none;">
                        <div class="time-display">
                            <span class="start-time"></span> - <span class="end-time"></span>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="changeClockBtn">Ubah</button>
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
                    <div class="time-badge" id="startTimeDisplay">07:00</div>
                </div>
                <div class="time-display-group">
                    <label>Jam Selesai:</label>
                    <div class="time-badge" id="endTimeDisplay">07:45</div>
                </div>
            </div>

            <div class="clocks-container">
                <div class="clock-wrapper">
                    <h6>Jam Mulai</h6>
                    <div class="clock-circle" id="startClock">
                        <div class="clock-face">
                            <!-- Numbers -->
                            @for ($i = 1; $i <= 12; $i++)
                                <div class="number" style="--i: {{ $i }}">{{ $i }}</div>
                            @endfor
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
                            @for ($i = 1; $i <= 12; $i++)
                                <div class="number" style="--i: {{ $i }}">{{ $i }}</div>
                            @endfor
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
/* Clock Picker Styles */
.analog-clock-container {
    position: relative;
}

.clock-picker-btn {
    width: 100%;
    padding: 12px 15px;
    text-align: left;
    border: 1px solid #ddd;
    border-radius: 8px;
    background: white;
    cursor: pointer;
    transition: all 0.3s ease;
}

.clock-picker-btn:hover {
    border-color: #007bff;
    background-color: #f8f9fa;
}

.selected-clock {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 15px;
    border: 2px solid #28a745;
    border-radius: 8px;
    background-color: #d4edda;
    margin-top: 10px;
}

.time-display {
    font-weight: 600;
    color: #155724;
    font-size: 16px;
}

.clock-picker-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.6);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
}

.clock-picker-content {
    background: white;
    border-radius: 15px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
    max-width: 800px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
}

.clock-picker-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 25px;
    border-bottom: 1px solid #eee;
    background-color: #f8f9fa;
    border-radius: 15px 15px 0 0;
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
}

.clock-picker-body {
    padding: 30px;
}

.time-displays-section {
    display: flex;
    justify-content: center;
    gap: 60px;
    margin-bottom: 40px;
}

.time-display-group {
    text-align: center;
}

.time-display-group label {
    display: block;
    margin-bottom: 12px;
    font-weight: 500;
    color: #666;
    font-size: 14px;
}

.time-badge {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white;
    padding: 12px 24px;
    border-radius: 25px;
    font-size: 18px;
    font-weight: 600;
    box-shadow: 0 4px 20px rgba(99, 102, 241, 0.3);
    min-width: 80px;
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
    color: #666;
    font-weight: 500;
    font-size: 14px;
}

.clock-circle {
    width: 180px;
    height: 180px;
    position: relative;
    margin: 0 auto;
}

.clock-face {
    width: 100%;
    height: 100%;
    border: 4px solid #333;
    border-radius: 50%;
    position: relative;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    box-shadow: 
        0 6px 20px rgba(0,0,0,0.1),
        inset 0 1px 0 rgba(255,255,255,0.8);
}

.number {
    position: absolute;
    font-size: 16px;
    font-weight: 600;
    color: #333;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.number[style*="--i: 12"] { top: 8px; left: 50%; transform: translateX(-50%); }
.number[style*="--i: 1"] { top: 22px; right: 28px; }
.number[style*="--i: 2"] { top: 45px; right: 12px; }
.number[style*="--i: 3"] { top: 50%; right: 8px; transform: translateY(-50%); }
.number[style*="--i: 4"] { bottom: 45px; right: 12px; }
.number[style*="--i: 5"] { bottom: 22px; right: 28px; }
.number[style*="--i: 6"] { bottom: 8px; left: 50%; transform: translateX(-50%); }
.number[style*="--i: 7"] { bottom: 22px; left: 28px; }
.number[style*="--i: 8"] { bottom: 45px; left: 12px; }
.number[style*="--i: 9"] { top: 50%; left: 8px; transform: translateY(-50%); }
.number[style*="--i: 10"] { top: 45px; left: 12px; }
.number[style*="--i: 11"] { top: 22px; left: 28px; }

.hand {
    position: absolute;
    border-radius: 3px;
    transform-origin: bottom center;
    left: 50%;
    bottom: 50%;
    cursor: grab;
    transition: all 0.1s ease;
}

.hand:active {
    cursor: grabbing;
}

.hour-hand {
    width: 4px;
    height: 50px;
    margin-left: -2px;
    background: #1e293b;
    z-index: 3;
}

.minute-hand {
    width: 3px;
    height: 70px;
    margin-left: -1.5px;
    background: #ef4444;
    z-index: 2;
}

.center-dot {
    position: absolute;
    width: 12px;
    height: 12px;
    background: #1e293b;
    border-radius: 50%;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 4;
    box-shadow: 0 0 8px rgba(0,0,0,0.3);
}

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
    background-color: #6b7280;
    color: white;
}

.btn-cancel:hover {
    background-color: #4b5563;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.btn-confirm {
    background-color: #10b981;
    color: white;
}

.btn-confirm:hover {
    background-color: #059669;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
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
    
    .clock-circle {
        width: 150px;
        height: 150px;
    }
    
    .hour-hand {
        height: 40px;
    }
    
    .minute-hand {
        height: 55px;
    }
    
    .number {
        font-size: 14px;
    }
}
</style>

<script>
// Auto-fill mata pelajaran
document.getElementById('guru_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const mapel = selectedOption.getAttribute('data-pengampu');
    document.getElementById('mapel').value = mapel;
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

// Digital displays
const startTimeDisplay = document.getElementById('startTimeDisplay');
const endTimeDisplay = document.getElementById('endTimeDisplay');

// Time values
let startTime = { hour: 7, minute: 0 };
let endTime = { hour: 7, minute: 45 };

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

    const formattedTime = `${time.hour.toString().padStart(2, '0')}:${time.minute.toString().padStart(2, '0')}`;
    display.textContent = formattedTime;
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

function setTime() {
    const startStr = `${startTime.hour.toString().padStart(2, '0')}:${startTime.minute.toString().padStart(2, '0')}`;
    const endStr = `${endTime.hour.toString().padStart(2, '0')}:${endTime.minute.toString().padStart(2, '0')}`;
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

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    if (!jamHidden.value) {
        e.preventDefault();
        alert('Silakan set waktu pelajaran terlebih dahulu!');
        openClockPicker();
    }
});
</script>
@endsection