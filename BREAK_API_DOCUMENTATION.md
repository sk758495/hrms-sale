# 🍽️ Break Management API - Exact Website Logic

## 🔐 Authentication
```
Authorization: Bearer {your_token}
Content-Type: application/json
```

---

## 🎯 Break Types & Behavior

### 1. **Lunch Break** 🍽️
- **Pauses work timer** automatically
- **Counts as break time** (not work)
- **Auto-pauses active work session**

### 2. **Short Break** ☕
- **Pauses work timer** automatically  
- **Counts as break time** (not work)
- **Auto-pauses active work session**

### 3. **Meeting** 👥
- **Does NOT pause work timer**
- **Counts as work time** (productive)
- **Work session continues running**

---

## 📋 API Endpoints

### 1. Start Break
**POST** `/api/breaks/start`

**Body for Lunch Break:**
```json
{
  "type": "lunch",
  "started_at": "2025-09-04T12:00:00Z",
  "session_id": 123
}
```

**Body for Short Break:**
```json
{
  "type": "short",
  "started_at": "2025-09-04T15:30:00Z", 
  "session_id": 123
}
```

**Body for Meeting:**
```json
{
  "type": "meeting",
  "started_at": "2025-09-04T14:00:00Z",
  "session_id": 123
}
```

**Response:**
```json
{
  "break_id": 456
}
```

**What Happens:**
- ✅ Break session created
- ✅ Activity logged
- ✅ **For lunch/short**: Work session auto-paused
- ✅ **For meeting**: Work session continues

---

### 2. End Break
**POST** `/api/breaks/end`

**Body:**
```json
{
  "break_id": 456,
  "ended_at": "2025-09-04T12:30:00Z"
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
**GET** `/api/breaks/active`

**Response (Active):**
```json
{
  "active": true,
  "break_id": 456,
  "type": "lunch",
  "start_time": "2025-09-04T12:00:00Z",
  "duration": 1800
}
```

**Response (No Active Break):**
```json
{
  "active": false
}
```

---

## 🔄 Complete Workflow Examples

### Lunch Break Workflow:
```bash
# 1. Start work session
curl -X POST "/api/timer/start" \
  -H "Authorization: Bearer {token}" \
  -d '{}'

# Response: {"session_id": 123}

# 2. Take lunch break (auto-pauses work)
curl -X POST "/api/breaks/start" \
  -H "Authorization: Bearer {token}" \
  -d '{
    "type": "lunch",
    "session_id": 123
  }'

# Response: {"break_id": 456}
# Work session 123 is now PAUSED automatically

# 3. End lunch break
curl -X POST "/api/breaks/end" \
  -H "Authorization: Bearer {token}" \
  -d '{
    "break_id": 456
  }'

# Response: {"success": true, "duration_seconds": 1800}

# 4. Resume work (manual)
curl -X POST "/api/timer/resume" \
  -H "Authorization: Bearer {token}" \
  -d '{}'

# Response: {"session_id": 124}
```

### Meeting Workflow:
```bash
# 1. Start work session
curl -X POST "/api/timer/start" \
  -H "Authorization: Bearer {token}" \
  -d '{}'

# Response: {"session_id": 123}

# 2. Start meeting (work continues)
curl -X POST "/api/breaks/start" \
  -H "Authorization: Bearer {token}" \
  -d '{
    "type": "meeting",
    "session_id": 123
  }'

# Response: {"break_id": 456}
# Work session 123 is still ACTIVE (not paused)

# 3. End meeting
curl -X POST "/api/breaks/end" \
  -H "Authorization: Bearer {token}" \
  -d '{
    "break_id": 456
  }'

# Response: {"success": true, "duration_seconds": 3600}
# Work session 123 is still ACTIVE
```

---

## ⚡ Key Features

### 🎯 **Exact Website Logic:**
- ✅ **Auto-pause for lunch/short breaks**
- ✅ **No pause for meetings**
- ✅ **Proper time calculations**
- ✅ **Activity logging**
- ✅ **Duplicate break prevention**

### 📊 **Time Calculations:**
- **Work Time** = Work Sessions + Meeting Time
- **Break Time** = Lunch + Short Breaks
- **Office Time** = Work + Break Time

### 🛡️ **Error Handling:**
- ✅ Prevents multiple active breaks
- ✅ Validates break types
- ✅ Handles missing sessions gracefully
- ✅ Proper error messages

---

## 🧪 Testing Scenarios

### Test 1: Lunch Break Auto-Pause
```json
POST /api/timer/start
{}

POST /api/breaks/start  
{
  "type": "lunch",
  "session_id": 1
}

GET /api/timer/active
// Should return: {"active": false} (paused)

POST /api/breaks/end
{
  "break_id": 1
}

GET /api/timer/active  
// Should return: {"active": false} (still paused)
```

### Test 2: Meeting No-Pause
```json
POST /api/timer/start
{}

POST /api/breaks/start
{
  "type": "meeting", 
  "session_id": 1
}

GET /api/timer/active
// Should return: {"active": true} (still running)

POST /api/breaks/end
{
  "break_id": 1
}

GET /api/timer/active
// Should return: {"active": true} (still running)
```

---

## 📈 Reports Integration

After breaks, check reports:
```bash
GET /api/reports/daily
```

**Response shows:**
```json
{
  "work_hours": "7h 30m",     // Sessions + Meetings
  "lunch_hours": "1h 0m",     // Lunch breaks only
  "short_break_hours": "0h 15m", // Short breaks only  
  "meeting_hours": "0h 30m",  // Meeting time (counted as work)
  "total_break_hours": "1h 15m", // Lunch + Short only
  "total_office_hours": "8h 45m" // Work + Breaks
}
```

The API now works **exactly like the website** with proper auto-pause logic! 🚀