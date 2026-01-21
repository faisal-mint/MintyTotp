<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Add Manual Entry - MintyOTP</title>
    <link rel="icon" type="image/png" href="<?php echo e(asset('MintyTotp-logo.png')); ?>">
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
            max-width: 600px;
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
            width: 120px;
            height: 100px;
            margin: 0 auto 5px;
            display: block;
            object-fit: contain;
        }

        .header h1 {
            font-size: 24px;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .header p {
            opacity: 0.9;
            font-size: 14px;
        }

        .content {
            padding: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #1e293b;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.2s;
            background: #f8fafc;
        }

        .form-group input:focus {
            outline: none;
            border-color: #3b82f6;
            background: white;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-primary {
            background: #3b82f6;
            color: white;
            width: 100%;
        }

        .btn-primary:hover {
            background: #2563eb;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        }

        .btn-secondary {
            background: #64748b;
            color: white;
            width: 100%;
            margin-top: 12px;
        }

        .btn-secondary:hover {
            background: #475569;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .footer {
            padding: 20px 30px;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            color: #64748b;
            font-size: 13px;
        }

        .footer a {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }

        .footer a:hover {
            color: #2563eb;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="<?php echo e(asset('MintyTotp-logo.png')); ?>" alt="MintyOTP" class="logo">
            <h1>Add Manual Entry</h1>
            <p>Enter your TOTP details manually</p>
        </div>

        <div class="content">
            <div id="alert" class="alert"></div>
            
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
                <a href="/" class="btn btn-secondary">Cancel</a>
            </form>
        </div>

        <div class="footer">
            Made with ❤️ by <a href="https://innovatify.io" target="_blank" rel="noopener noreferrer">Innovatify</a>
        </div>
    </div>

    <script>
        function getCsrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.content || '';
        }

        function showAlert(message, type) {
            const alert = document.getElementById('alert');
            alert.textContent = message;
            alert.className = `alert alert-${type}`;
            alert.style.display = 'block';
            setTimeout(() => {
                alert.style.display = 'none';
            }, 5000);
        }

        document.getElementById('manual-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData);
            
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
                    showAlert('TOTP entry added successfully!', 'success');
                    setTimeout(() => {
                        window.location.href = '/';
                    }, 1000);
                } else {
                    const error = await response.json();
                    showAlert('Error: ' + (error.message || 'Failed to add entry'), 'error');
                }
            } catch (error) {
                console.error('Error adding entry:', error);
                showAlert('Failed to add entry', 'error');
            }
        });
    </script>
</body>
</html>
<?php /**PATH D:\wamp\www\MintyOTP\resources\views/add-manual.blade.php ENDPATH**/ ?>