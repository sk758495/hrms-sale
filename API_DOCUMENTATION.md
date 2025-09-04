# 🚀 HRMS Time Management API Documentation

## 🔐 Authentication
All employee APIs require Bearer token authentication:
```
Authorization: Bearer {your_token}
```

---

## ⏱️ Timer Management APIs

### 1. Start Work Session
**POST** `/api/employee/timer/start`

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Content-Type": "application/json"
}
```

**Body:**
```json
{
  "started_at": "2025-09-03T10:00:00Z" // Optional, defaults to now
}
```

**Response:**
```json
{
  "session_id": 123
}
```

**Error Response:**
```json
{
  "error": "You already have an active session",
  "session_id": 122
}
```

---

### 2. Pause Work Session
**POST** `/api/employee/timer/pause`

**Body:**
```json
{
  "session_id": 123,
  "paused_at": "2025-09-03T12:00:00Z", // Optional
  "reason": "Manual pause" // Optional
}
```

**Response:**
```json
{
  "success": true
}
```

---

### 3. Resume Work Session
**POST** `/api/employee/timer/resume`

**Body:**
```json
{
  "resumed_at": "2025-09-03T13:00:00Z" // Optional
}
```

**Response:**
```json
{
  "session_id": 124
}
```

---

### 4. Stop Work Session
**POST** `/api/employee/timer/stop`

**Body:**
```json
{
  "session_id": 123,
  "stopped_at": "2025-09-03T18:00:00Z" // Optional
}
```

**Response:**
```json
{
  "success": true
}
```

---

### 5. Add Manual Time
**POST** `/api/employee/timer/manual`

**Body:**
```json
{
  "seconds": 3600, // Required: time in seconds
  "at": "2025-09-03T15:00:00Z", // Optional: when to add time
  "note": "Forgot to start timer" // Optional: reason
}
```

**Response:**
```json
{
  "session_id": 125
}
```

---

### 6. Get Active Session
**GET** `/api/employee/timer/active`

**Response:**
```json
{
  "active": true,
  "session_id": 123,
  "start_time": "2025-09-03T10:00:00Z",
  "duration": 7200 // seconds since start
}
```

**No Active Session:**
```json
{
  "active": false
}
```

---

## ☕ Break Management APIs

### 1. Start Break
**POST** `/api/employee/breaks/start`

**Body:**
```json
{
  "type": "lunch", // Required: "lunch", "short", "meeting"
  "started_at": "2025-09-03T12:00:00Z", // Optional
  "session_id": 123 // Optional: current work session
}
```

**Response:**
```json
{
  "break_id": 456
}
```

**Break Types:**
- `lunch` - Pauses work timer, counts as break time
- `short` - Pauses work timer, counts as break time  
- `meeting` - Does NOT pause work timer, counts as work time

---

### 2. End Break
**POST** `/api/employee/breaks/end`

**Body:**
```json
{
  "break_id": 456,
  "ended_at": "2025-09-03T12:30:00Z" // Optional
}
```

**Response:**
```json
{
  "success": true,
  "duration_seconds": 1800
}
```

---

### 3. Get Active Break
**GET** `/api/employee/breaks/active`

**Response:**
```json
{
  "active": true,
  "break_id": 456,
  "type": "lunch",
  "start_time": "2025-09-03T12:00:00Z",
  "duration": 1800 // seconds since start
}
```

---

## 📊 Reports APIs

### 1. Daily Report
**GET** `/api/employee/reports/daily?date=2025-09-03`

**Response:**
```json
{
  "date": "2025-09-03",
  "work_seconds": 28800, // 8 hours
  "work_hours": "8h 0m",
  "lunch_seconds": 3600,
  "lunch_hours": "1h 0m",
  "short_break_seconds": 900,
  "short_break_hours": "0h 15m",
  "meeting_seconds": 1800,
  "meeting_hours": "0h 30m",
  "total_break_seconds": 4500,
  "total_break_hours": "1h 15m",
  "total_office_seconds": 33300,
  "total_office_hours": "9h 15m",
  "missing_seconds": 0,
  "missing_hours": "0h 0m",
  "sessions": [...],
  "breaks": [...]
}
```

---

### 2. Weekly Report
**GET** `/api/employee/reports/weekly?date=2025-09-03`

**Response:**
```json
{
  "week_start": "2025-09-02",
  "week_end": "2025-09-08",
  "work_seconds": 144000,
  "work_hours": "40h 0m",
  "lunch_hours": "5h 0m",
  "short_break_hours": "1h 15m",
  "meeting_hours": "2h 30m",
  "total_break_hours": "6h 15m",
  "total_office_hours": "46h 15m",
  "sessions_count": 25,
  "breaks_count": 15
}
```

---

### 3. Monthly Report
**GET** `/api/employee/reports/monthly?date=2025-09-03`

**Response:**
```json
{
  "month": "2025-09",
  "month_name": "September 2025",
  "work_seconds": 576000,
  "work_hours": "160h 0m",
  "lunch_hours": "20h 0m",
  "short_break_hours": "5h 0m",
  "meeting_hours": "10h 0m",
  "total_break_hours": "25h 0m",
  "total_office_hours": "185h 0m",
  "working_days": 20,
  "sessions_count": 100,
  "breaks_count": 60
}
```

---

## 🔑 Authentication APIs

### Login (Auto-starts timer)
**POST** `/api/auth/login`

**Body:**
```json
{
  "email": "employee@example.com",
  "password": "password"
}
```

**Response:**
```json
{
  "token": "1|abc123...",
  "user": {...},
  "session_id": 123, // Auto-started session
  "auto_started": true
}
```

---

## ⚡ Key Features

### 🎯 **Perfect Time Calculation:**
- **Work Time** = Work Sessions + Meeting Time
- **Break Time** = Lunch Breaks + Short Breaks
- **Total Office Time** = Work Time + Break Time

### 🔄 **Auto Features:**
- ✅ Auto-start timer on login
- ✅ Auto-pause after 10 minutes inactivity
- ✅ Duplicate session prevention
- ✅ Real-time duration calculation

### 📱 **Break Types:**
1. **Lunch Break** - Pauses timer, counts as break
2. **Short Break** - Pauses timer, counts as break
3. **Meeting** - Continues timer, counts as work

### 🛡️ **Error Handling:**
All APIs return proper HTTP status codes and error messages for validation failures, authentication issues, and server errors.

---

## 🧪 Testing Examples

### Start a work session:
```bash
curl -X POST "https://your-domain.com/api/employee/timer/start" \
  -H "Authorization: Bearer your_token" \
  -H "Content-Type: application/json"
```

### Take a lunch break:
```bash
curl -X POST "https://your-domain.com/api/employee/breaks/start" \
  -H "Authorization: Bearer your_token" \
  -H "Content-Type: application/json" \
  -d '{"type": "lunch", "session_id": 123}'
```

### Get daily report:
```bash
curl -X GET "https://your-domain.com/api/employee/reports/daily?date=2025-09-03" \
  -H "Authorization: Bearer your_token"
```

The API is now **100% identical** to website functionality! 🚀