@extends('layouts.employee-app')

@section('title', 'My Attendance & Work Hours')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
<style>
.stats-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}
.stats-card.success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
.stats-card.warning { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.stats-card.info { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.stats-card.primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }

.work-summary {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    margin-bottom: 20px;
}

.time-display {
    font-size: 2.5rem;
    font-weight: bold;
    margin: 10px 0;
}

.time-label {
    font-size: 0.9rem;
    opacity: 0.8;
    margin-bottom: 5px;
}

.progress-custom {
    height: 8px;
    border-radius: 10px;
    background: rgba(255,255,255,0.3);
}

.progress-custom .progress-bar {
    border-radius: 10px;
}

.attendance-table {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
}

.status-indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 8px;
}

.qr-section {
    background: white;
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
}
</style>
@endpush

@section('content')
<div class="container-fluid mt-4">
    <!-- Work Hours Summary Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="stats-card success">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="time-label">Today's Work</div>
                        <div class="time-display" id="today-work">0h 0m</div>
                        <small>Break: <span id="today-break">0h 0m</span></small>
                    </div>
                    <i class="bi bi-clock-history" style="font-size: 3rem; opacity: 0.3;"></i>
                </div>
                <div class="progress-custom mt-3">
                    <div class="progress-bar bg-white" id="today-progress" style="width: 0%"></div>
                </div>
                <small class="mt-2 d-block">Target: 8 hours</small>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="stats-card info">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="time-label">This Week</div>
                        <div class="time-display" id="week-work">0h 0m</div>
                        <small>Days: <span id="week-days">0</span></small>
                    </div>
                    <i class="bi bi-calendar-week" style="font-size: 3rem; opacity: 0.3;"></i>
                </div>
                <div class="progress-custom mt-3">
                    <div class="progress-bar bg-white" id="week-progress" style="width: 0%"></div>
                </div>
                <small class="mt-2 d-block">Target: 40 hours</small>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="stats-card warning">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="time-label">This Month</div>
                        <div class="time-display" id="month-work">0h 0m</div>
                        <small>Days: <span id="month-days">0</span></small>
                    </div>
                    <i class="bi bi-calendar-month" style="font-size: 3rem; opacity: 0.3;"></i>
                </div>
                <div class="progress-custom mt-3">
                    <div class="progress-bar bg-white" id="month-progress" style="width: 0%"></div>
                </div>
                <small class="mt-2 d-block">Target: 160 hours</small>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="stats-card primary">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="time-label">Current Session</div>
                        <div class="time-display" id="current-session">Inactive</div>
                        <small id="session-duration">00:00:00</small>
                    </div>
                    <i class="bi bi-stopwatch" style="font-size: 3rem; opacity: 0.3;"></i>
                </div>
                <div class="mt-3">
                    <button class="btn btn-light btn-sm" onclick="window.location.href='{{ route('emp.time-tracker') }}'">
                        <i class="bi bi-play-circle"></i> Time Tracker
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Status & QR Scanner -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="work-summary">
                <h5 class="mb-4"><i class="bi bi-calendar-check text-success"></i> Today's Attendance</h5>
                <div id="today-attendance">
                    @if($todayAttendance)
                        <div class="row">
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="status-indicator bg-{{ $todayAttendance->status == 'present' ? 'success' : ($todayAttendance->status == 'late' ? 'warning' : 'danger') }}"></div>
                                    <strong>Status</strong>
                                    <div class="h4 mt-2" id="status-badge">{{ ucfirst($todayAttendance->status) }}</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <i class="bi bi-box-arrow-in-right text-success mb-2" style="font-size: 1.5rem;"></i>
                                    <div><strong>Check In</strong></div>
                                    <div class="h5 mt-2" id="check-in-time">{{ $todayAttendance->check_in ? $todayAttendance->check_in->format('H:i:s') : 'Not checked in' }}</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <i class="bi bi-box-arrow-right text-danger mb-2" style="font-size: 1.5rem;"></i>
                                    <div><strong>Check Out</strong></div>
                                    <div class="h5 mt-2" id="check-out-time">{{ $todayAttendance->check_out ? $todayAttendance->check_out->format('H:i:s') : 'Not checked out' }}</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <i class="bi bi-clock text-info mb-2" style="font-size: 1.5rem;"></i>
                                    <div><strong>Office Hours</strong></div>
                                    <div class="h5 mt-2" id="working-hours">{{ number_format($todayAttendance->working_hours, 1) }} hours</div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center text-muted" id="no-record">
                            <i class="bi bi-calendar-x" style="font-size: 3rem; opacity: 0.3;"></i>
                            <p class="mt-3">No attendance record for today</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="qr-section">
                <h5 class="mb-3"><i class="bi bi-qr-code-scan text-info"></i> QR Scanner</h5>
                <div class="text-center">
                    <div id="qr-container" style="display: none;">
                        <canvas id="qr-canvas"></canvas>
                        <p class="mt-2">
                            <small class="text-muted">Expires at: <span id="qr-expiry"></span></small>
                        </p>
                    </div>
                    <button id="generate-qr" class="btn btn-primary">
                        <i class="bi bi-qr-code"></i> Generate QR Code
                    </button>
                    <div id="scan-result" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Work Hours Breakdown -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="work-summary">
                <h5 class="mb-4"><i class="bi bi-graph-up text-primary"></i> Work Hours Breakdown</h5>
                <div class="row">
                    <div class="col-md-2">
                        <div class="text-center">
                            <div class="h4 text-success" id="total-work-time">0h 0m</div>
                            <small class="text-muted">Work Time</small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="text-center">
                            <div class="h4 text-warning" id="total-lunch-time">0h 0m</div>
                            <small class="text-muted">Lunch</small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="text-center">
                            <div class="h4 text-info" id="total-short-time">0h 0m</div>
                            <small class="text-muted">Short Breaks</small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="text-center">
                            <div class="h4 text-primary" id="total-meeting-time">0h 0m</div>
                            <small class="text-muted">Meetings</small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="text-center">
                            <div class="h4 text-secondary" id="total-break-time">0h 0m</div>
                            <small class="text-muted">Total Breaks</small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="text-center">
                            <div class="h4 text-dark" id="total-office-time">0h 0m</div>
                            <small class="text-muted">Office Time</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Attendance Table -->
    <div class="attendance-table">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="bi bi-calendar3"></i> Monthly Attendance - {{ now()->format('F Y') }}</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Office Hours</th>
                            <th>Work Hours</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $attendance)
                            <tr>
                                <td>
                                    <strong>{{ $attendance->date->format('d M Y') }}</strong>
                                    <br><small class="text-muted">{{ $attendance->date->format('l') }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $attendance->status == 'present' ? 'success' : ($attendance->status == 'late' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($attendance->status) }}
                                    </span>
                                </td>
                                <td>{{ $attendance->check_in ? $attendance->check_in->format('H:i:s') : '-' }}</td>
                                <td>{{ $attendance->check_out ? $attendance->check_out->format('H:i:s') : '-' }}</td>
                                <td>{{ number_format($attendance->office_hours, 1) }} hours</td>
                                <td>
                                    <span class="work-hours-cell" data-date="{{ $attendance->date->format('Y-m-d') }}">
                                        <div class="spinner-border spinner-border-sm" role="status"></div>
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="bi bi-calendar-x" style="font-size: 2rem; opacity: 0.3;"></i>
                                    <p class="mt-2">No attendance records found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="scan-loading" class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background: rgba(0,0,0,0.8); z-index: 9999; display: none !important;">
        <div class="text-center text-white">
            <div class="spinner-border mb-3" role="status"></div>
            <h5>Processing Attendance...</h5>
            <p>Please wait while we process your scan</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentQrData = null;
let qrExpiry = null;

$(document).ready(function() {
    loadWorkHoursSummary();
    loadCurrentSession();
    loadWorkHoursForAttendance();
    
    // Refresh data every 30 seconds
    setInterval(function() {
        loadWorkHoursSummary();
        loadCurrentSession();
    }, 30000);
});

function loadWorkHoursSummary() {
    // Load today's data
    $.get('/emp-management/reports/daily').done(function(data) {
        $('#today-work').text(data.work_hours);
        $('#today-break').text(data.total_break_hours);
        
        // Update breakdown
        $('#total-work-time').text(data.work_hours);
        $('#total-lunch-time').text(data.lunch_hours);
        $('#total-short-time').text(data.short_break_hours);
        $('#total-meeting-time').text(data.meeting_hours);
        $('#total-break-time').text(data.total_break_hours);
        $('#total-office-time').text(data.total_office_hours);
        
        // Calculate progress (8 hours = 100%)
        const workSeconds = data.work_seconds || 0;
        const progressPercent = Math.min((workSeconds / 28800) * 100, 100);
        $('#today-progress').css('width', progressPercent + '%');
    });
    
    // Load weekly data
    $.get('/emp-management/reports/weekly').done(function(data) {
        $('#week-work').text(data.work_hours);
        $('#week-days').text(data.working_days || 0);
        
        const workSeconds = data.work_seconds || 0;
        const progressPercent = Math.min((workSeconds / 144000) * 100, 100); // 40 hours
        $('#week-progress').css('width', progressPercent + '%');
    });
    
    // Load monthly data
    $.get('/emp-management/reports/monthly').done(function(data) {
        $('#month-work').text(data.work_hours);
        $('#month-days').text(data.working_days || 0);
        
        const workSeconds = data.work_seconds || 0;
        const progressPercent = Math.min((workSeconds / 576000) * 100, 100); // 160 hours
        $('#month-progress').css('width', progressPercent + '%');
    });
}

function loadCurrentSession() {
    $.get('/emp-management/timer/active').done(function(data) {
        if (data.active) {
            $('#current-session').text('Active');
            const duration = data.duration || 0;
            $('#session-duration').text(formatTime(duration));
        } else {
            $('#current-session').text('Inactive');
            $('#session-duration').text('00:00:00');
        }
    });
}

function loadWorkHoursForAttendance() {
    $('.work-hours-cell').each(function() {
        const cell = $(this);
        const date = cell.data('date');
        
        $.get('/emp-management/reports/daily?date=' + date).done(function(data) {
            const workHours = data.work_hours || '0h 0m';
            const breakHours = data.total_break_hours || '0h 0m';
            const officeHours = data.total_office_hours || '0h 0m';
            
            cell.html(`
                <div class="text-success fw-bold">${workHours}</div>
                <small class="text-muted">Break: ${breakHours}</small><br>
                <small class="text-info">Office: ${officeHours}</small>
            `);
        }).fail(function() {
            cell.html('<span class="text-muted">-</span>');
        });
    });
}

function formatTime(seconds) {
    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    const secs = seconds % 60;
    return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
}

// QR Code functionality (keeping existing code)
document.getElementById('generate-qr').addEventListener('click', function() {
    generateQrCode();
});

function generateQrCode() {
    const button = document.getElementById('generate-qr');
    button.disabled = true;
    button.innerHTML = '<i class="bi bi-hourglass-split"></i> Generating...';
    
    fetch('/attendance/generate-qr', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            button.disabled = false;
            button.innerHTML = '<i class="bi bi-qr-code"></i> Generate QR Code';
            alert('Error: ' + data.error);
            return;
        }

        currentQrData = data.qr_data;
        const qrContainer = document.getElementById('qr-container');
        const canvas = document.getElementById('qr-canvas');
        
        canvas.style.display = 'none';
        const existingImg = qrContainer.querySelector('img');
        if (existingImg) existingImg.remove();
        
        const qrImg = document.createElement('img');
        qrImg.src = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(data.qr_data)}`;
        qrImg.style.border = '1px solid #ddd';
        qrImg.style.borderRadius = '8px';
        qrImg.style.cursor = 'pointer';
        qrImg.alt = 'QR Code';
        
        qrImg.onload = function() {
            qrContainer.insertBefore(qrImg, qrContainer.firstChild);
            qrContainer.style.display = 'block';
            document.getElementById('qr-expiry').textContent = data.expires_at;
            
            button.disabled = false;
            button.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Refresh QR Code';
            
            const instruction = document.createElement('p');
            instruction.innerHTML = '<small class="text-info"><i class="bi bi-hand-index"></i> Click QR code to scan for attendance</small>';
            instruction.id = 'qr-instruction';
            qrContainer.appendChild(instruction);
        };
        
        qrImg.onerror = function() {
            button.disabled = false;
            button.innerHTML = '<i class="bi bi-qr-code"></i> Generate QR Code';
            alert('Failed to load QR image');
        };
    })
    .catch(error => {
        button.disabled = false;
        button.innerHTML = '<i class="bi bi-qr-code"></i> Generate QR Code';
        alert('Failed to generate QR code: ' + error.message);
    });
}

// QR scanning functionality (keeping existing code)
document.getElementById('qr-container').addEventListener('click', function(e) {
    if (!currentQrData) return;
    
    if (e.target.tagName === 'IMG' || e.target.tagName === 'CANVAS') {
        document.getElementById('scan-loading').style.display = 'flex';
        
        if (!navigator.geolocation) {
            document.getElementById('scan-loading').style.display = 'none';
            document.getElementById('scan-result').innerHTML = '<div class="alert alert-danger">Geolocation not supported</div>';
            return;
        }

        navigator.geolocation.getCurrentPosition(function(position) {
            scanQrCode(position.coords.latitude, position.coords.longitude);
        }, function(error) {
            document.getElementById('scan-loading').style.display = 'none';
            document.getElementById('scan-result').innerHTML = '<div class="alert alert-danger">Please enable location access to scan QR code</div>';
        });
    }
});

function scanQrCode(latitude, longitude) {
    const token = currentQrData.split('/').pop();
    
    fetch(`/attendance/scan/${token}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            latitude: latitude,
            longitude: longitude
        })
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('scan-loading').style.display = 'none';
        
        if (data.success) {
            updateTodayAttendance(data);
            loadWorkHoursSummary(); // Refresh work hours
            
            const actionText = data.action === 'check_in' ? 'Check In' : 'Check Out';
            document.getElementById('scan-result').innerHTML = 
                `<div class="alert alert-success">
                    <i class="bi bi-check-circle"></i> ${actionText} Successful!<br>
                    <small>Time: ${data.time} | Status: ${data.status}</small>
                </div>`;
        } else {
            document.getElementById('scan-result').innerHTML = 
                `<div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle"></i> ${data.error}
                </div>`;
        }
    })
    .catch(error => {
        document.getElementById('scan-loading').style.display = 'none';
        document.getElementById('scan-result').innerHTML = 
            `<div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle"></i> Scan failed: ${error.message}
            </div>`;
    });
}

function updateTodayAttendance(data) {
    const todayAttendanceDiv = document.getElementById('today-attendance');
    const noRecord = document.getElementById('no-record');
    
    if (noRecord) {
        todayAttendanceDiv.innerHTML = `
            <div class="row">
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="status-indicator bg-${data.status === 'present' ? 'success' : (data.status === 'late' ? 'warning' : 'danger')}"></div>
                        <strong>Status</strong>
                        <div class="h4 mt-2" id="status-badge">${data.status.charAt(0).toUpperCase() + data.status.slice(1)}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <i class="bi bi-box-arrow-in-right text-success mb-2" style="font-size: 1.5rem;"></i>
                        <div><strong>Check In</strong></div>
                        <div class="h5 mt-2" id="check-in-time">${data.action === 'check_in' ? data.time : 'Not checked in'}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <i class="bi bi-box-arrow-right text-danger mb-2" style="font-size: 1.5rem;"></i>
                        <div><strong>Check Out</strong></div>
                        <div class="h5 mt-2" id="check-out-time">${data.action === 'check_out' ? data.time : 'Not checked out'}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <i class="bi bi-clock text-info mb-2" style="font-size: 1.5rem;"></i>
                        <div><strong>Office Hours</strong></div>
                        <div class="h5 mt-2" id="working-hours">${data.working_hours || '0'} hours</div>
                    </div>
                </div>
            </div>
        `;
    } else {
        const statusBadge = document.getElementById('status-badge');
        if (statusBadge) {
            statusBadge.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);
        }
        
        if (data.action === 'check_in') {
            document.getElementById('check-in-time').textContent = data.time;
        } else if (data.action === 'check_out') {
            document.getElementById('check-out-time').textContent = data.time;
            if (data.working_hours) {
                document.getElementById('working-hours').textContent = data.working_hours + ' hours';
            }
        }
    }
}
</script>
@endpush