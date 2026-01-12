<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>MintyOTP - TOTP Authenticator</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f5f7fa;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header .logo {
            width: 64px;
            height: 64px;
            margin: 0 auto 15px;
            display: block;
        }

        .header h1 {
            font-size: 28px;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .header p {
            opacity: 0.9;
            font-size: 14px;
        }

        .toolbar {
            padding: 20px 30px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: #3b82f6;
            color: white;
        }

        .btn-primary:hover {
            background: #2563eb;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        }

        .btn-secondary {
            background: #64748b;
            color: white;
        }

        .btn-secondary:hover {
            background: #475569;
        }

        .btn-danger {
            background: #ef4444;
            color: white;
            padding: 6px 12px;
            font-size: 12px;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .content {
            padding: 30px;
        }

        .totp-list {
            display: grid;
            gap: 16px;
        }

        .totp-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.2s;
        }

        .totp-card:hover {
            border-color: #3b82f6;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1);
        }

        .totp-info {
            flex: 1;
        }

        .totp-name {
            font-size: 18px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 6px;
        }

        .totp-timer {
            font-size: 13px;
            color: #64748b;
            margin-top: 8px;
        }

        .progress-bar {
            width: 100%;
            height: 3px;
            background: #e2e8f0;
            border-radius: 2px;
            margin-top: 10px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #3b82f6 0%, #1e3a8a 100%);
            transition: width 1s linear;
        }

        .totp-code {
            font-size: 36px;
            font-weight: 700;
            color: #1e3a8a;
            letter-spacing: 6px;
            font-family: 'Courier New', monospace;
            margin-right: 24px;
            min-width: 180px;
            text-align: center;
        }

        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: #94a3b8;
        }

        .empty-state svg {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            opacity: 0.4;
            display: block;
        }

        .empty-state h3 {
            font-size: 20px;
            color: #64748b;
            margin-bottom: 10px;
        }

        .empty-state p {
            font-size: 14px;
            margin-bottom: 24px;
        }

        .actions {
            display: flex;
            gap: 12px;
            align-items: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="<?php echo e(asset('MintyTotp-logo.png')); ?>" alt="MintyOTP" class="logo">
            <h1>MintyOTP</h1>
            <p>Your Secure TOTP Authenticator</p>
        </div>

        <div class="toolbar">
            <div>
                <a href="/add/manual" class="btn btn-primary">+ Add Manual Entry</a>
                <a href="/add/uri" class="btn btn-secondary" style="margin-left: 10px;">+ Add from URI</a>
            </div>
        </div>

        <div class="content">
            <div class="totp-list" id="totp-list">
                <div class="empty-state">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                    <h3>No TOTP entries yet</h3>
                    <p>Get started by adding your first TOTP entry</p>
                    <div>
                        <a href="/add/manual" class="btn btn-primary">Add Manual Entry</a>
                        <a href="/add/uri" class="btn btn-secondary" style="margin-left: 10px;">Add from URI</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let refreshIntervals = {};

        function getCsrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.content || '';
        }

        async function loadTotpEntries() {
            try {
                const response = await fetch('/api/totp');
                const entries = await response.json();
                
                const list = document.getElementById('totp-list');
                
                if (entries.length === 0) {
                    list.innerHTML = `
                        <div class="empty-state">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                            <h3>No TOTP entries yet</h3>
                            <p>Get started by adding your first TOTP entry</p>
                            <div>
                                <a href="/add/manual" class="btn btn-primary">Add Manual Entry</a>
                                <a href="/add/uri" class="btn btn-secondary" style="margin-left: 10px;">Add from URI</a>
                            </div>
                        </div>
                    `;
                    return;
                }

                list.innerHTML = entries.map(entry => `
                    <div class="totp-card" data-id="${entry.id}">
                        <div class="totp-info">
                            <div class="totp-name">${entry.display_name}</div>
                            <div class="totp-timer">Refreshes in <span class="timer-${entry.id}">${entry.remaining_seconds}</span>s</div>
                            <div class="progress-bar">
                                <div class="progress-fill timer-progress-${entry.id}" style="width: ${(entry.remaining_seconds / entry.period) * 100}%"></div>
                            </div>
                        </div>
                        <div class="totp-code" id="code-${entry.id}">${entry.code}</div>
                        <div class="actions">
                            <button class="btn btn-danger" onclick="deleteEntry(${entry.id})">Delete</button>
                        </div>
                    </div>
                `).join('');

                entries.forEach(entry => {
                    setupAutoRefresh(entry);
                });
            } catch (error) {
                console.error('Error loading TOTP entries:', error);
            }
        }

        function setupAutoRefresh(entry) {
            if (refreshIntervals[entry.id]) {
                clearInterval(refreshIntervals[entry.id]);
            }

            let remaining = entry.remaining_seconds;
            const period = entry.period;

            updateTimer(entry.id, remaining, period);

            refreshIntervals[entry.id] = setInterval(async () => {
                remaining--;

                if (remaining <= 0) {
                    try {
                        const response = await fetch(`/api/totp/${entry.id}`);
                        const data = await response.json();
                        document.getElementById(`code-${entry.id}`).textContent = data.code;
                        remaining = data.remaining_seconds;
                    } catch (error) {
                        console.error('Error refreshing code:', error);
                    }
                }

                updateTimer(entry.id, remaining, period);
            }, 1000);
        }

        function updateTimer(id, remaining, period) {
            const timerEl = document.querySelector(`.timer-${id}`);
            const progressEl = document.querySelector(`.timer-progress-${id}`);
            
            if (timerEl) {
                timerEl.textContent = remaining;
            }
            
            if (progressEl) {
                const percentage = (remaining / period) * 100;
                progressEl.style.width = `${percentage}%`;
            }
        }

        async function deleteEntry(id) {
            if (!confirm('Are you sure you want to delete this TOTP entry?')) {
                return;
            }

            try {
                const response = await fetch(`/api/totp/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Content-Type': 'application/json'
                    }
                });

                if (response.ok) {
                    if (refreshIntervals[id]) {
                        clearInterval(refreshIntervals[id]);
                        delete refreshIntervals[id];
                    }
                    loadTotpEntries();
                }
            } catch (error) {
                console.error('Error deleting entry:', error);
                alert('Failed to delete entry');
            }
        }

        loadTotpEntries();
    </script>
</body>
</html>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/php8_projects/MintyOTP/resources/views/index.blade.php ENDPATH**/ ?>