@extends('layouts.employee-app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Time Tracker</h3>
                </div>
                <div class="card-body">
                    <!-- Session Status -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h5>Current Session</h5>
                                    <h3 id="session-status">Inactive</h3>
                                    <p id="session-time">00:00:00</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h5>Today's Work Time</h5>
                                    <h3 id="total-work-time">0h 0m</h3>
                                    <p>Break Time: <span id="total-break-time">0h 0m</span></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Control Buttons -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="btn-group d-flex" role="group">
                                <button type="button" class="btn btn-success" id="start-work" onclick="startWork()">
                                    <i class="fas fa-play"></i> Start Work
                                </button>
                                <button type="button" class="btn btn-warning" id="pause-work" onclick="pauseWork()" disabled>
                                    <i class="fas fa-pause"></i> Pause Work
                                </button>
                                <button type="button" class="btn btn-danger" id="stop-work" onclick="stopWork()" disabled>
                                    <i class="fas fa-stop"></i> Stop Work
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Break Buttons -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5>Break Options</h5>
                            <div class="btn-group d-flex" role="group">
                                <button type="button" class="btn btn-info" onclick="startBreak('lunch')">
                                    <i class="fas fa-utensils"></i> Lunch Break
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="startBreak('short')">
                                    <i class="fas fa-coffee"></i> Short Break
                                </button>
                                <button type="button" class="btn btn-primary" onclick="startBreak('meeting')">
                                    <i class="fas fa-users"></i> Meeting
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Manual Time Entry -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Add Manual Time Entry</h5>
                                </div>
                                <div class="card-body">
                                    <form id="manual-time-form">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label>Hours</label>
                                                <input type="number" class="form-control" id="manual-hours" min="0" max="12" value="0">
                                            </div>
                                            <div class="col-md-3">
                                                <label>Minutes</label>
                                                <input type="number" class="form-control" id="manual-minutes" min="0" max="59" value="0">
                                            </div>
                                            <div class="col-md-4">
                                                <label>Reason</label>
                                                <input type="text" class="form-control" id="manual-reason" placeholder="Reason for manual entry" required>
                                            </div>
                                            <div class="col-md-2">
                                                <label>&nbsp;</label>
                                                <button type="submit" class="btn btn-primary form-control">Add Time</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Missing Time Alert -->
                    <div id="missing-time-alert" class="alert alert-warning" style="display: none;">
                        <h5>Missing Time Detected</h5>
                        <p>You have <span id="missing-duration"></span> of missing time. Please add manual entry to account for this time.</p>
                    </div>

                    <!-- Today's Activity -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Today's Activity</h5>
                                    <button class="btn btn-sm btn-primary float-right" onclick="refreshActivity()">
                                        <i class="fas fa-refresh"></i> Refresh
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div id="activity-log"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Break Modal -->
<div class="modal fade" id="breakModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="breakModalTitle">Break in Progress</h5>
            </div>
            <div class="modal-body">
                <p>Break Type: <strong id="break-type"></strong></p>
                <p>Duration: <strong id="break-duration">00:00:00</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="endBreak()">End Break</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
let currentSessionId = null;
let currentBreakId = null;
let sessionTimer = null;
let breakTimer = null;
let lastActivityTime = Date.now();
let inactivityTimer = null;
let sessionStartTime = null;
let breakStartTime = null;

const INACTIVITY_TIMEOUT = 10 * 60 * 1000; // 10 minutes

$(document).ready(function() {
    checkActiveSession();
    loadTodayActivity();
    startInactivityMonitor();
    startRealTimeUpdates();
    
    // Track user activity
    $(document).on('click keypress mousemove', function() {
        lastActivityTime = Date.now();
    });

    $('#manual-time-form').on('submit', function(e) {
        e.preventDefault();
        addManualTime();
    });
});

function startRealTimeUpdates() {
    // Update today's totals every 30 seconds
    setInterval(function() {
        updateTodayTotals();
    }, 30000);
    
    // Initial load
    updateTodayTotals();
}

function updateTodayTotals() {
    $.get('/emp-management/reports/daily').done(function(response) {
        $('#total-work-time').text(response.work_hours);
        $('#total-break-time').text(response.lunch_hours + ' + ' + response.short_break_hours);
    });
}

function checkActiveSession() {
    $.get('/emp-management/timer/active').done(function(response) {
        if (response.active) {
            currentSessionId = response.session_id;
            const startTime = new Date(response.start_time);
            sessionStartTime = startTime.getTime();
            
            $('#session-status').text('Active');
            $('#start-work').prop('disabled', true);
            $('#pause-work, #stop-work').prop('disabled', false);
            
            startSessionTimer();
        }
    }).fail(function() {
        console.log('No active session found');
    });
    
    // Check for active breaks
    $.get('/emp-management/breaks/active').done(function(response) {
        if (response.active) {
            currentBreakId = response.break_id;
            const startTime = new Date(response.start_time);
            breakStartTime = startTime.getTime();
            
            const typeNames = {
                'lunch': 'Lunch Break',
                'short': 'Short Break',
                'meeting': 'Meeting'
            };
            
            $('#break-type').text(typeNames[response.type]);
            $('#breakModal').modal('show');
            startBreakTimer();
        }
    }).fail(function() {
        console.log('No active break found');
    });
}

function startInactivityMonitor() {
    inactivityTimer = setInterval(function() {
        if (currentSessionId && Date.now() - lastActivityTime > INACTIVITY_TIMEOUT) {
            autoStopSession();
        }
    }, 30000); // Check every 30 seconds
}

function autoStopSession() {
    if (currentSessionId) {
        $.post('/emp-management/timer/pause', {
            session_id: currentSessionId,
            paused_at: new Date(lastActivityTime + INACTIVITY_TIMEOUT).toISOString(),
            reason: 'Auto-pause due to inactivity'
        }).done(function() {
            showMissingTimeAlert();
            resetSessionUI();
        });
    }
}

function showMissingTimeAlert() {
    const missingTime = Math.floor((Date.now() - lastActivityTime - INACTIVITY_TIMEOUT) / 1000);
    const hours = Math.floor(missingTime / 3600);
    const minutes = Math.floor((missingTime % 3600) / 60);
    
    $('#missing-duration').text(`${hours}h ${minutes}m`);
    $('#missing-time-alert').show();
}

function startWork() {
    $.post('/emp-management/timer/start', {
        started_at: new Date().toISOString()
    }).done(function(response) {
        currentSessionId = response.session_id;
        sessionStartTime = Date.now();
        $('#session-status').text('Active');
        $('#start-work').prop('disabled', true);
        $('#pause-work, #stop-work').prop('disabled', false);
        startSessionTimer();
        updateTodayTotals();
        loadTodayActivity();
    }).fail(function(xhr) {
        alert('Error: ' + xhr.responseJSON.error);
    });
}

function pauseWork() {
    if (currentSessionId) {
        $.post('/emp-management/timer/pause', {
            session_id: currentSessionId,
            paused_at: new Date().toISOString(),
            reason: 'Manual pause'
        }).done(function() {
            resetSessionUI();
            updateTodayTotals();
            loadTodayActivity();
        });
    }
}

function stopWork() {
    if (currentSessionId) {
        $.post('/emp-management/timer/stop', {
            session_id: currentSessionId,
            stopped_at: new Date().toISOString()
        }).done(function() {
            resetSessionUI();
            updateTodayTotals();
            loadTodayActivity();
        });
    }
}

function resetSessionUI() {
    currentSessionId = null;
    sessionStartTime = null;
    $('#session-status').text('Inactive');
    $('#session-time').text('00:00:00');
    $('#start-work').prop('disabled', false);
    $('#pause-work, #stop-work').prop('disabled', true);
    if (sessionTimer) {
        clearInterval(sessionTimer);
        sessionTimer = null;
    }
}

function startSessionTimer() {
    sessionTimer = setInterval(function() {
        if (sessionStartTime) {
            const elapsed = Math.floor((Date.now() - sessionStartTime) / 1000);
            $('#session-time').text(formatTime(elapsed));
            
            // Update totals every minute during active session
            if (elapsed % 60 === 0) {
                updateTodayTotals();
            }
        }
    }, 1000);
}

function startBreak(type) {
    const typeNames = {
        'lunch': 'Lunch Break',
        'short': 'Short Break', 
        'meeting': 'Meeting'
    };

    $.post('/emp-management/breaks/start', {
        type: type,
        started_at: new Date().toISOString(),
        session_id: currentSessionId
    }).done(function(response) {
        currentBreakId = response.break_id;
        breakStartTime = Date.now();
        $('#break-type').text(typeNames[type]);
        $('#breakModal').modal('show');
        startBreakTimer();
        
        if (type !== 'meeting' && currentSessionId) {
            pauseWork(); // Auto-pause work for lunch/short breaks
        }
    }).fail(function(xhr) {
        alert('Error: ' + xhr.responseJSON.error);
    });
}

function endBreak() {
    if (currentBreakId) {
        $.post('/emp-management/breaks/end', {
            break_id: currentBreakId,
            ended_at: new Date().toISOString()
        }).done(function() {
            currentBreakId = null;
            breakStartTime = null;
            $('#breakModal').modal('hide');
            if (breakTimer) {
                clearInterval(breakTimer);
                breakTimer = null;
            }
            updateTodayTotals();
            loadTodayActivity();
        });
    }
}

function startBreakTimer() {
    breakTimer = setInterval(function() {
        if (breakStartTime) {
            const elapsed = Math.floor((Date.now() - breakStartTime) / 1000);
            $('#break-duration').text(formatTime(elapsed));
            
            // Update totals every minute during break
            if (elapsed % 60 === 0) {
                updateTodayTotals();
            }
        }
    }, 1000);
}

function addManualTime() {
    const hours = parseInt($('#manual-hours').val()) || 0;
    const minutes = parseInt($('#manual-minutes').val()) || 0;
    const reason = $('#manual-reason').val().trim();
    
    if (hours === 0 && minutes === 0) {
        alert('Please enter time to add');
        return;
    }
    
    if (!reason) {
        alert('Please provide a reason');
        return;
    }
    
    const seconds = (hours * 3600) + (minutes * 60);
    
    $.post('/emp-management/timer/manual', {
        seconds: seconds,
        at: new Date().toISOString(),
        note: reason
    }).done(function() {
        $('#manual-hours, #manual-minutes').val(0);
        $('#manual-reason').val('');
        $('#missing-time-alert').hide();
        updateTodayTotals();
        loadTodayActivity();
        alert('Manual time entry added successfully');
    }).fail(function(xhr) {
        alert('Error: ' + xhr.responseJSON.error);
    });
}

function loadTodayActivity() {
    $.get('/emp-management/reports/daily').done(function(response) {
        updateTodayTotals();
        
        let html = '<div class="table-responsive"><table class="table table-sm">';
        html += '<thead><tr><th>Time</th><th>Action</th><th>Description</th></tr></thead><tbody>';
        
        // Work sessions
        response.sessions.forEach(function(session) {
            const startTime = new Date(session.start_time).toLocaleTimeString();
            const endTime = session.end_time ? new Date(session.end_time).toLocaleTimeString() : 'Ongoing';
            const duration = session.duration_seconds ? formatTime(session.duration_seconds) : '';
            
            html += `<tr class="table-success">
                <td>${startTime} - ${endTime}</td>
                <td>Work Session</td>
                <td>${duration} ${session.end_reason}</td>
            </tr>`;
        });
        
        // Breaks
        response.breaks.forEach(function(breakItem) {
            const startTime = new Date(breakItem.start_time).toLocaleTimeString();
            const endTime = breakItem.end_time ? new Date(breakItem.end_time).toLocaleTimeString() : 'Ongoing';
            const duration = breakItem.duration_seconds ? formatTime(breakItem.duration_seconds) : '';
            const typeClass = breakItem.type === 'meeting' ? 'table-info' : 'table-warning';
            
            html += `<tr class="${typeClass}">
                <td>${startTime} - ${endTime}</td>
                <td>${breakItem.type.charAt(0).toUpperCase() + breakItem.type.slice(1)} Break</td>
                <td>${duration}</td>
            </tr>`;
        });
        
        html += '</tbody></table></div>';
        $('#activity-log').html(html);
    });
}

function refreshActivity() {
    loadTodayActivity();
}

function formatTime(seconds) {
    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    const secs = seconds % 60;
    return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
}
</script>
@endsection