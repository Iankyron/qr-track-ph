<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR-Track PH | Entrance Kiosk Terminal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="h-full font-sans antialiased text-slate-100 flex flex-col justify-between p-4 sm:p-8" x-data="kioskApp()">

    <header class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 max-w-xl mx-auto w-full bg-slate-800/50 p-4 sm:p-5 rounded-2xl border border-slate-700/60 shadow-lg">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-blue-600 flex items-center justify-center text-white font-bold text-xl sm:text-2xl shadow">
                <i class="fa-solid fa-qrcode"></i>
            </div>
            <div>
                <h1 class="font-bold text-base sm:text-lg tracking-wide text-white">QR-Track Terminal</h1>
                <span class="text-[10px] text-sky-400 uppercase tracking-widest font-semibold">Official Entrance Kiosk</span>
            </div>
        </div>
        <div class="text-left sm:text-right w-full sm:w-auto flex sm:flex-col justify-between sm:justify-start items-center sm:items-end border-t sm:border-t-0 pt-2 sm:pt-0 border-slate-700">
            <span class="text-lg sm:text-xl font-mono font-bold text-white" x-text="currentTime"></span>
            <span class="text-[10px] text-emerald-400 font-medium flex items-center"><i class="fa-solid fa-circle text-[6px] mr-1 animate-pulse"></i> Connected</span>
        </div>
    </header>

    <main class="max-w-xl mx-auto w-full space-y-6 my-4">
        <div class="bg-slate-800 p-5 sm:p-8 rounded-3xl border border-slate-700 shadow-2xl space-y-6 text-center">
            
            <div>
                <h2 class="text-lg sm:text-xl font-bold text-white">Scan Your ID Badge</h2>
                <p class="text-xs text-slate-400 mt-1">Position your QR code inside the camera frame below.</p>
            </div>

            <div class="relative bg-slate-900 rounded-2xl p-4 overflow-hidden shadow-inner flex flex-col items-center justify-center min-h-[260px] sm:min-h-[300px] border border-slate-700">
                <div id="reader" class="w-full max-w-sm rounded-xl overflow-hidden" style="display: none;"></div>
                
                <div id="scanner-placeholder" class="flex flex-col items-center justify-center py-6">
                    <div class="w-20 h-20 sm:w-24 sm:h-24 border-2 border-sky-400/50 rounded-2xl relative flex items-center justify-center mb-4 shadow-lg bg-slate-800">
                        <i class="fa-solid fa-camera text-3xl sm:text-4xl text-sky-400 animate-pulse"></i>
                    </div>
                    <p class="text-xs text-slate-300 font-medium mb-4">Camera is currently inactive.</p>
                </div>

                <div class="flex space-x-3 z-10 w-full sm:w-auto justify-center">
                    <button id="start-camera-btn" @click="startScanner()" class="w-full sm:w-auto px-6 py-3 bg-blue-600 hover:bg-blue-500 text-sm font-bold rounded-xl transition flex items-center justify-center space-x-2 shadow-lg">
                        <i class="fa-solid fa-video"></i>
                        <span>Activate Camera Scanner</span>
                    </button>
                    <button id="stop-camera-btn" @click="stopScanner()" style="display: none;" class="w-full sm:w-auto px-6 py-3 bg-red-600 hover:bg-red-500 text-sm font-bold rounded-xl transition flex items-center justify-center space-x-2 shadow-lg">
                        <i class="fa-solid fa-video-slash"></i>
                        <span>Stop Camera</span>
                    </button>
                </div>
            </div>

            <div class="space-y-2 pt-2 border-t border-slate-700/60 text-left">
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Manual Employee Code Entry</label>
                <div class="flex flex-col sm:flex-row gap-2">
                    <input type="text" x-model="manualCodeInput" @keyup.enter="processAttendanceByCode(manualCodeInput)" placeholder="e.g. EMP-101" class="flex-1 px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white focus:border-blue-500 text-sm outline-none">
                    <button @click="processAttendanceByCode(manualCodeInput)" class="w-full sm:w-auto bg-slate-700 hover:bg-slate-600 text-white px-6 py-3 rounded-xl text-sm font-bold transition">
                        Submit
                    </button>
                </div>
            </div>

        </div>
    </main>

    <footer class="text-center text-xs text-slate-500 py-2">
        <a href="admin.php" class="hover:text-sky-400 underline transition">Switch to Secure Management Dashboard (Admin)</a>
    </footer>

    <div x-show="successModal.show" class="fixed inset-0 z-50 bg-slate-950/85 backdrop-blur-md flex items-center justify-center p-4" x-cloak>
        <div class="bg-slate-800 border-2 border-emerald-500/50 w-full max-w-sm rounded-3xl shadow-2xl p-6 sm:p-8 text-center space-y-6 transform transition-all animate-bounce-short">
            <div class="w-20 h-20 bg-emerald-500/20 text-emerald-400 rounded-full flex items-center justify-center mx-auto text-4xl shadow-inner animate-pulse">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="space-y-1">
                <h3 class="font-extrabold text-2xl text-white">Attendance Triggered!</h3>
                <p class="text-xs text-emerald-400 font-semibold uppercase tracking-wider">Successfully Logged</p>
            </div>
            <div class="bg-slate-900 p-4 rounded-2xl border border-slate-700 space-y-2 text-left text-xs">
                <div class="flex justify-between"><span class="text-slate-400">Employee:</span> <strong class="text-white font-bold text-sm" x-text="successModal.name"></strong></div>
                <div class="flex justify-between"><span class="text-slate-400">Department:</span> <strong class="text-slate-200" x-text="successModal.department"></strong></div>
                <div class="flex justify-between"><span class="text-slate-400">Action:</span> <span class="px-2.5 py-0.5 bg-emerald-500/20 text-emerald-400 rounded-full font-bold" x-text="successModal.action"></span></div>
                <div class="flex justify-between"><span class="text-slate-400">Timestamp:</span> <strong class="text-sky-400 font-mono" x-text="successModal.time"></strong></div>
            </div>
            <button @click="successModal.show = false" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-2xl font-bold text-sm shadow-lg transition">
                Dismiss / Next Scan
            </button>
        </div>
    </div>

    <div class="fixed bottom-6 right-6 z-50 max-w-xs" x-show="toast.show" x-transition>
        <div class="bg-red-900/90 border border-red-700 text-red-200 px-4 py-3 rounded-2xl shadow-xl flex items-center space-x-3 text-xs">
            <i class="fa-solid fa-circle-exclamation text-red-400 text-base"></i>
            <span x-text="toast.message"></span>
        </div>
    </div>

    <script>
        function kioskApp() {
            return {
                currentTime: '',
                manualCodeInput: '',
                html5QrCode: null,
                employees: [],
                attendanceLogs: [],
                toast: { show: false, message: '' },
                successModal: { show: false, name: '', department: '', action: '', time: '' },

                init() {
                    this.fetchData();
                    setInterval(() => {
                        const now = new Date();
                        this.currentTime = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                    }, 1000);
                },

                async fetchData() {
                    try {
                        const res = await fetch('api.php?action=get_all');
                        if (res.ok) {
                            const data = await res.json();
                            this.employees = data.employees || [];
                            this.attendanceLogs = data.attendanceLogs || [];
                        }
                    } catch (e) {}
                },

                showToast(message) {
                    this.toast.message = message;
                    this.toast.show = true;
                    setTimeout(() => { this.toast.show = false; }, 4000);
                },

                playSuccessBeep() {
                    try {
                        const ctx = new (window.AudioContext || window.webkitAudioContext)();
                        const osc = ctx.createOscillator();
                        const gain = ctx.createGain();
                        osc.type = 'sine';
                        osc.frequency.setValueAtTime(587.33, ctx.currentTime);
                        gain.gain.setValueAtTime(0.1, ctx.currentTime);
                        osc.connect(gain);
                        gain.connect(ctx.destination);
                        osc.start();
                        osc.stop(ctx.currentTime + 0.15);
                    } catch (e) {}
                },

                startScanner() {
                    document.getElementById('scanner-placeholder').style.display = 'none';
                    document.getElementById('reader').style.display = 'block';
                    document.getElementById('start-camera-btn').style.display = 'none';
                    document.getElementById('stop-camera-btn').style.display = 'inline-flex';

                    this.html5QrCode = new Html5Qrcode("reader");
                    this.html5QrCode.start(
                        { facingMode: "environment" }, 
                        { fps: 10, qrbox: { width: 220, height: 220 } },
                        (decodedText) => {
                            this.stopScanner();
                            this.processAttendanceByCode(decodedText);
                        },
                        () => {}
                    ).catch(() => {
                        this.showToast('Camera access failed or denied.');
                        this.stopScanner();
                    });
                },

                stopScanner() {
                    if (this.html5QrCode) {
                        this.html5QrCode.stop().then(() => this.html5QrCode.clear()).catch(() => {});
                    }
                    const ph = document.getElementById('scanner-placeholder');
                    const rd = document.getElementById('reader');
                    const sb = document.getElementById('start-camera-btn');
                    const tb = document.getElementById('stop-camera-btn');
                    if (ph) ph.style.display = 'flex';
                    if (rd) rd.style.display = 'none';
                    if (sb) sb.style.display = 'inline-flex';
                    if (tb) tb.style.display = 'none';
                },

                async processAttendanceByCode(code) {
                    if (!code) return;
                    await this.fetchData();
                    const emp = this.employees.find(e => e.idCode.toLowerCase() === code.trim().toLowerCase());
                    
                    if (!emp) {
                        this.showToast(`Employee ID "${code}" not found.`);
                        return;
                    }

                    const todayLogs = this.attendanceLogs.filter(l => l.employeeName === emp.name);
                    let nextAction = 'Time In';
                    if (todayLogs.length > 0) {
                        const last = todayLogs[0].action;
                        if (last === 'Time In') nextAction = 'Break Out';
                        else if (last === 'Break Out') nextAction = 'Break In';
                        else if (last === 'Break In') nextAction = 'Time Out';
                    }

                    const now = new Date();
                    const timeStr = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
                    const fullTimestamp = now.toISOString().slice(0, 19).replace('T', ' ');

                    const newLog = {
                        id: Date.now(),
                        employeeName: emp.name,
                        department: emp.department,
                        action: nextAction,
                        timeStr: timeStr,
                        fullTimestamp: fullTimestamp
                    };

                    try {
                        const res = await fetch('api.php?action=log_attendance', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify(newLog)
                        });
                        if (res.ok) {
                            this.playSuccessBeep();
                            this.successModal = {
                                show: true,
                                name: emp.name,
                                department: emp.department,
                                action: nextAction,
                                time: timeStr + ' (' + fullTimestamp.split(' ')[0] + ')'
                            };
                            this.manualCodeInput = '';
                            
                            setTimeout(() => {
                                this.successModal.show = false;
                            }, 4000);
                        }
                    } catch (e) {
                        this.showToast('Network error saving attendance.');
                    }
                }
            }
        }
    </script>
</body>
</html>