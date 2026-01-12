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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .header p {
            opacity: 0.9;
        }

        .content {
            padding: 30px;
        }

        .add-form {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-small {
            padding: 6px 12px;
            font-size: 12px;
        }

        .totp-list {
            display: grid;
            gap: 15px;
        }

        .totp-card {
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s;
        }

        .totp-card:hover {
            border-color: #667eea;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .totp-info {
            flex: 1;
        }

        .totp-name {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .totp-issuer {
            font-size: 14px;
            color: #666;
        }

        .totp-code {
            font-size: 32px;
            font-weight: 700;
            color: #667eea;
            letter-spacing: 4px;
            font-family: 'Courier New', monospace;
            margin-right: 20px;
        }

        .totp-timer {
            font-size: 12px;
            color: #999;
            margin-top: 5px;
        }

        .progress-bar {
            width: 100%;
            height: 4px;
            background: #e0e0e0;
            border-radius: 2px;
            margin-top: 10px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            transition: width 1s linear;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .empty-state svg {
            width: 80px;
            height: 80px;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .tab {
            padding: 10px 20px;
            background: #f8f9fa;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }

        .tab.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 MintyOTP</h1>
            <p>Your Secure TOTP Authenticator</p>
        </div>

        <div class="content">
            <div class="tabs">
                <button class="tab active" onclick="switchTab('manual')">Manual Entry</button>
                <button class="tab" onclick="switchTab('uri')">QR Code / URI</button>
            </div>

            <div id="manual-tab" class="tab-content active">
                <div class="add-form">
                    <h2 style="margin-bottom: 20px;">Add New TOTP Entry</h2>
                    <form id="manual-form">
                        <div class="form-group">
                            <label for="name">Account Name *</label>
                            <input type="text" id="name" name="name" required placeholder="e.g., john@example.com">
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="secret">Secret Key *</label>
                                <input type="text" id="secret" name="secret" required placeholder="Base32 encoded secret">
                            </div>
                            <div class="form-group">
                                <label for="issuer">Issuer (Optional)</label>
                                <input type="text" id="issuer" name="issuer" placeholder="e.g., Google, GitHub">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="digits">Digits</label>
                                <input type="number" id="digits" name="digits" value="6" min="6" max="8">
                            </div>
                            <div class="form-group">
                                <label for="period">Period (seconds)</label>
                                <input type="number" id="period" name="period" value="30" min="15" max="300">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Add TOTP Entry</button>
                    </form>
                </div>
            </div>

            <div id="uri-tab" class="tab-content">
                <div class="add-form">
                    <h2 style="margin-bottom: 20px;">Add from QR Code / URI</h2>
                    <form id="uri-form">
                        <div class="form-group">
                            <label for="uri">TOTP URI</label>
                            <input type="text" id="uri" name="uri" required placeholder="otpauth://totp/...">
                        </div>
                        <button type="submit" class="btn btn-primary">Add from URI</button>
                    </form>
                </div>
            </div>

            <div class="totp-list" id="totp-list">
                <div class="empty-state">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                    <p>No TOTP entries yet. Add your first entry above.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        let refreshIntervals = {};

        function getCsrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.content || '';
        }

        function switchTab(tab) {
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
            
            if (tab === 'manual') {
                document.querySelector('.tab:first-child').classList.add('active');
                document.getElementById('manual-tab').classList.add('active');
            } else {
                document.querySelector('.tab:last-child').classList.add('active');
                document.getElementById('uri-tab').classList.add('active');
            }
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
                            <p>No TOTP entries yet. Add your first entry above.</p>
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
                        <button class="btn btn-danger btn-small" onclick="deleteEntry(${entry.id})">Delete</button>
                    </div>
                `).join('');

                // Setup auto-refresh for each entry
                entries.forEach(entry => {
                    setupAutoRefresh(entry);
                });
            } catch (error) {
                console.error('Error loading TOTP entries:', error);
            }
        }

        function setupAutoRefresh(entry) {
            // Clear existing interval if any
            if (refreshIntervals[entry.id]) {
                clearInterval(refreshIntervals[entry.id]);
            }

            let remaining = entry.remaining_seconds;
            const period = entry.period;

            // Update immediately
            updateTimer(entry.id, remaining, period);

            // Refresh every second
            refreshIntervals[entry.id] = setInterval(async () => {
                remaining--;

                if (remaining <= 0) {
                    // Fetch new code
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

        document.getElementById('manual-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData);
            
            // Convert digits and period to integers
            data.digits = parseInt(data.digits);
            data.period = parseInt(data.period);

            try {
                const response = await fetch('/api/totp', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken()
                    },
                    body: JSON.stringify(data)
                });

                if (response.ok) {
                    e.target.reset();
                    loadTotpEntries();
                } else {
                    const error = await response.json();
                    alert('Error: ' + (error.message || 'Failed to add entry'));
                }
            } catch (error) {
                console.error('Error adding entry:', error);
                alert('Failed to add entry');
            }
        });

        document.getElementById('uri-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const uri = formData.get('uri');

            try {
                const response = await fetch('/api/totp/uri', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken()
                    },
                    body: JSON.stringify({ uri })
                });

                if (response.ok) {
                    e.target.reset();
                    loadTotpEntries();
                } else {
                    const error = await response.json();
                    alert('Error: ' + (error.error || error.message || 'Failed to add entry'));
                }
            } catch (error) {
                console.error('Error adding entry:', error);
                alert('Failed to add entry');
            }
        });

        // Load entries on page load
        loadTotpEntries();
    </script>
</body>
</html>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/php8_projects/MintyOTP/resources/views/welcome.blade.php ENDPATH**/ ?>