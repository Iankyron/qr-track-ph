<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR-Track PH | Admin Management Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { navy: '#0F172A', blue: '#2563EB', accent: '#38BDF8' }
                    }
                }
            }
        }
    </script>
    <style>[x-cloak] { display: none !important; }</style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="h-full font-sans antialiased text-slate-800" x-data="adminApp()">

    <div x-show="!isAuthenticated" class="fixed inset-0 z-50 bg-slate-900/90 backdrop-blur-md flex items-center justify-center p-4" x-cloak>
        <div class="bg-white w-full max-w-sm rounded-3xl shadow-2xl p-8 text-center space-y-6">
            <div class="w-16 h-16 bg-blue-100 text-brand-blue rounded-2xl flex items-center justify-center mx-auto text-2xl shadow-inner">
                <i class="fa-solid fa-lock"></i>
            </div>
            <div class="space-y-1">
                <h3 class="font-extrabold text-xl text-slate-800">Admin Authentication</h3>
                <p class="text-xs text-slate-500">Enter security passcode to manage the dashboard.</p>
            </div>
            <form @submit.prevent="login()" class="space-y-4">
                <input type="password" x-model="passcode" required placeholder="Enter Passcode (e.g. admin123)" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm text-center outline-none focus:border-brand-blue font-mono">
                <button type="submit" class="w-full py-3 bg-brand-blue hover:bg-blue-700 text-white rounded-xl font-semibold text-sm shadow-md transition">Unlock Dashboard</button>
            </form>
            <div class="text-[11px] text-slate-400">
                Default demo passcode: <strong class="text-slate-600 font-mono">admin123</strong>
            </div>
        </div>
    </div>

    <div class="min-h-full flex flex-col lg:flex-row relative">
        
        <div x-show="mobileMenuOpen" @click="mobileMenuOpen = false" class="fixed inset-0 z-30 bg-slate-900/50 lg:hidden" x-cloak></div>

        <aside :class="mobileMenuOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'" class="fixed lg:static inset-y-0 left-0 z-40 w-64 bg-brand-navy text-slate-300 flex flex-col justify-between shrink-0 shadow-xl transition-transform duration-300">
            <div>
                <div class="h-20 flex items-center justify-between px-6 bg-slate-900 border-b border-slate-800">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-lg bg-brand-blue flex items-center justify-center text-white font-bold text-xl">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <div>
                            <span class="block font-bold text-white text-base">Admin Portal</span>
                            <span class="block text-[10px] uppercase text-brand-accent">Management Suite</span>
                        </div>
                    </div>
                    <button @click="mobileMenuOpen = false" class="lg:hidden text-slate-400 hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
                </div>

                <nav class="p-4 space-y-1.5 text-sm font-medium">
                    <button @click="currentTab = 'employees'; mobileMenuOpen = false" :class="currentTab === 'employees' ? 'bg-brand-blue text-white shadow-lg' : 'hover:bg-slate-800 hover:text-white'" class="w-full flex items-center space-x-3 px-4 py-3 rounded-xl transition">
                        <i class="fa-solid fa-users w-5 text-center"></i>
                        <span>Employee Directory</span>
                    </button>
                    <button @click="currentTab = 'logs'; mobileMenuOpen = false" :class="currentTab === 'logs' ? 'bg-brand-blue text-white shadow-lg' : 'hover:bg-slate-800 hover:text-white'" class="w-full flex items-center space-x-3 px-4 py-3 rounded-xl transition">
                        <i class="fa-solid fa-clock-rotate-left w-5 text-center"></i>
                        <span>Attendance Logs</span>
                    </button>
                    <button @click="currentTab = 'summary'; mobileMenuOpen = false" :class="currentTab === 'summary' ? 'bg-brand-blue text-white shadow-lg' : 'hover:bg-slate-800 hover:text-white'" class="w-full flex items-center space-x-3 px-4 py-3 rounded-xl transition">
                        <i class="fa-solid fa-file-invoice-dollar w-5 text-center"></i>
                        <span>Payroll DTR Summary</span>
                    </button>
                </nav>
            </div>

            <div class="p-6 bg-slate-900/50 border-t border-slate-800 text-xs text-slate-400 space-y-3">
                <a href="kiosk.php" class="block w-full py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white text-center rounded-xl font-bold transition shadow">
                    <i class="fa-solid fa-camera-retro mr-1"></i> Open Kiosk Scanner
                </a>
                <button @click="fetchData()" class="w-full py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-lg transition">
                    <i class="fa-solid fa-rotate mr-1"></i> Refresh Data
                </button>
                <button @click="logout()" class="w-full py-2 bg-red-500/10 hover:bg-red-500/20 text-red-400 rounded-lg font-medium transition border border-red-500/20">
                    <i class="fa-solid fa-lock mr-1"></i> Lock Admin Session
                </button>
            </div>
        </aside>

        <main class="flex-1 flex flex-col min-w-0 overflow-y-auto">
            <header class="h-20 bg-white border-b border-slate-200 px-4 sm:px-10 flex items-center justify-between sticky top-0 z-20 shadow-sm">
                <div class="flex items-center space-x-3">
                    <button @click="mobileMenuOpen = true" class="lg:hidden text-slate-600 p-2"><i class="fa-solid fa-bars text-xl"></i></button>
                    <h1 class="text-lg sm:text-2xl font-bold text-slate-800 capitalize" x-text="getPageTitle()"></h1>
                </div>
                <button @click="openAddModal = true" class="bg-brand-blue hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl text-sm font-semibold shadow transition flex items-center space-x-2">
                    <i class="fa-solid fa-user-plus"></i>
                    <span>Add Employee</span>
                </button>
            </header>

            <div class="p-4 sm:p-10 flex-1">
                
                <div x-show="currentTab === 'employees'" class="space-y-6">
                    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                        <input type="text" x-model="searchQuery" placeholder="Search employee..." class="w-full sm:max-w-md px-4 py-2.5 rounded-xl border border-slate-300 text-sm outline-none">
                    </div>
                    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <template x-for="emp in filteredEmployees" :key="emp.id">
                            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="font-bold text-slate-800" x-text="emp.name"></h3>
                                        <span class="text-xs text-brand-blue font-semibold" x-text="emp.idCode"></span>
                                    </div>
                                    <button @click="deleteEmployee(emp.id)" class="text-slate-400 hover:text-red-500"><i class="fa-solid fa-trash-can text-xs"></i></button>
                                </div>
                                <div class="text-xs text-slate-600 bg-slate-50 p-3 rounded-xl space-y-1">
                                    <div class="flex justify-between"><span>Dept:</span> <strong x-text="emp.department"></strong></div>
                                    <div class="flex justify-between"><span>Position:</span> <strong x-text="emp.position"></strong></div>
                                </div>
                                <button @click="showBadge(emp)" class="w-full py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold text-xs flex items-center justify-center space-x-2">
                                    <i class="fa-solid fa-qrcode text-brand-blue"></i><span>View QR Badge</span>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                <div x-show="currentTab === 'logs'" class="space-y-6" x-cloak>
                    <div class="bg-white p-4 sm:p-6 rounded-2xl border border-slate-200 shadow-sm grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Search Employee / Dept</label>
                            <input type="text" x-model="logFilters.search" @input="logPage = 1" placeholder="Filter by name or dept..." class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Filter by Action</label>
                            <select x-model="logFilters.action" @change="logPage = 1" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm outline-none bg-white">
                                <option value="">All Actions</option>
                                <option value="Time In">Time In</option>
                                <option value="Break Out">Break Out</option>
                                <option value="Break In">Break In</option>
                                <option value="Time Out">Time Out</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Filter by Date</label>
                            <input type="date" x-model="logFilters.date" @change="logPage = 1" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm outline-none bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Rows per Page</label>
                            <select x-model.number="logPerPage" @change="logPage = 1" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm outline-none bg-white">
                                <option value="10">10 rows</option>
                                <option value="25">25 rows</option>
                                <option value="50">50 rows</option>
                            </select>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden p-4 sm:p-6 space-y-4">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
                            <h3 class="font-bold text-lg text-slate-800">Attendance History Logs</h3>
                            <span class="text-xs text-slate-500">
                                Showing 
                                <strong class="text-slate-800" x-text="paginatedLogs.length > 0 ? ((logPage - 1) * logPerPage) + 1 : 0"></strong> - 
                                <strong class="text-slate-800" x-text="Math.min(logPage * logPerPage, filteredLogs.length)"></strong> of 
                                <strong class="text-slate-800" x-text="filteredLogs.length"></strong> entries
                            </span>
                        </div>

                        <!-- MOBILE CARD VIEW FOR LOGS -->
                        <div class="space-y-3 sm:hidden">
                            <template x-for="log in paginatedLogs" :key="log.id">
                                <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 space-y-2 shadow-sm">
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-sm text-slate-800" x-text="log.employeeName"></span>
                                        <span :class="{
                                            'bg-emerald-100 text-emerald-800': log.action === 'Time In',
                                            'bg-amber-100 text-amber-800': log.action === 'Break Out',
                                            'bg-blue-100 text-blue-800': log.action === 'Break In',
                                            'bg-purple-100 text-purple-800': log.action === 'Time Out'
                                        }" class="px-2.5 py-0.5 rounded-full text-xs font-bold" x-text="log.action"></span>
                                    </div>
                                    <div class="text-xs text-slate-500 flex justify-between">
                                        <span>Dept: <strong class="text-slate-700" x-text="log.department"></strong></span>
                                        <span class="font-mono text-[11px]" x-text="log.fullTimestamp"></span>
                                    </div>
                                </div>
                            </template>
                            <div x-show="filteredLogs.length === 0" class="text-center py-8 text-slate-400 text-sm">
                                No logs match your filter criteria.
                            </div>
                        </div>

                        <!-- DESKTOP TABLE VIEW FOR LOGS -->
                        <div class="hidden sm:block overflow-x-auto">
                            <table class="w-full text-left border-collapse text-sm">
                                <thead>
                                    <tr class="bg-slate-50 text-slate-600 uppercase text-[11px] border-b border-slate-200">
                                        <th class="py-3 px-4">Employee</th>
                                        <th class="py-3 px-4">Department</th>
                                        <th class="py-3 px-4">Action</th>
                                        <th class="py-3 px-4">Timestamp</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <template x-for="log in paginatedLogs" :key="log.id">
                                        <tr class="hover:bg-slate-50">
                                            <td class="py-3 px-4 font-bold text-slate-800" x-text="log.employeeName"></td>
                                            <td class="py-3 px-4 text-slate-600" x-text="log.department"></td>
                                            <td class="py-3 px-4"><span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800" x-text="log.action"></span></td>
                                            <td class="py-3 px-4 text-slate-600 font-mono text-xs" x-text="log.fullTimestamp"></td>
                                        </tr>
                                    </template>
                                    <tr x-show="filteredLogs.length === 0">
                                        <td colspan="4" class="text-center py-8 text-slate-400 text-sm">No logs match your filter criteria.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="flex items-center justify-between pt-4 border-t border-slate-100 text-xs" x-show="filteredLogs.length > 0">
                            <button @click="if(logPage > 1) logPage--" :disabled="logPage === 1" :class="logPage === 1 ? 'opacity-40 cursor-not-allowed bg-slate-50' : 'hover:bg-slate-100 bg-white'" class="px-4 py-2 rounded-xl border border-slate-200 font-semibold text-slate-700 transition">
                                <i class="fa-solid fa-chevron-left mr-1"></i> Previous
                            </button>
                            
                            <span class="text-slate-600 font-medium">
                                Page <strong class="text-slate-800" x-text="logPage"></strong> of <strong class="text-slate-800" x-text="totalPages || 1"></strong>
                            </span>

                            <button @click="if(logPage < totalPages) logPage++" :disabled="logPage >= totalPages" :class="logPage >= totalPages ? 'opacity-40 cursor-not-allowed bg-slate-50' : 'hover:bg-slate-100 bg-white'" class="px-4 py-2 rounded-xl border border-slate-200 font-semibold text-slate-700 transition">
                                Next <i class="fa-solid fa-chevron-right ml-1"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div x-show="currentTab === 'summary'" class="space-y-6" x-cloak>
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden p-4 sm:p-6">
                        <h3 class="font-bold text-lg text-slate-800 mb-4">Payroll DTR Summary</h3>
                        
                        <!-- MOBILE CARD VIEW FOR SUMMARY -->
                        <div class="space-y-3 sm:hidden">
                            <template x-for="emp in employees" :key="emp.id">
                                <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 space-y-2 shadow-sm">
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-sm text-slate-800" x-text="emp.name"></span>
                                        <span class="px-2.5 py-0.5 bg-blue-50 text-brand-blue rounded-full text-xs font-semibold" x-text="getEmployeeTotalHours(emp.name) + ' hrs'"></span>
                                    </div>
                                    <div class="text-xs text-slate-500 flex justify-between">
                                        <span>Dept: <strong class="text-slate-700" x-text="emp.department"></strong></span>
                                        <span>Present: <strong class="text-slate-700" x-text="getEmployeeDaysPresent(emp.name) + ' days'"></strong></span>
                                    </div>
                                </div>
                            </template>
                            <div x-show="employees.length === 0" class="text-center py-8 text-slate-400 text-sm">
                                No employee records found.
                            </div>
                        </div>

                        <!-- DESKTOP TABLE VIEW FOR SUMMARY -->
                        <div class="hidden sm:block overflow-x-auto">
                            <table class="w-full text-left border-collapse text-sm">
                                <thead>
                                    <tr class="bg-slate-50 text-slate-600 uppercase text-[11px] border-b border-slate-200">
                                        <th class="py-3 px-4">Employee</th>
                                        <th class="py-3 px-4">Department</th>
                                        <th class="py-3 px-4">Days Present</th>
                                        <th class="py-3 px-4">Total Hours</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <template x-for="emp in employees" :key="emp.id">
                                        <tr class="hover:bg-slate-50">
                                            <td class="py-3 px-4 font-bold text-slate-800" x-text="emp.name"></td>
                                            <td class="py-3 px-4 text-slate-600" x-text="emp.department"></td>
                                            <td class="py-3 px-4 font-semibold" x-text="getEmployeeDaysPresent(emp.name)"></td>
                                            <td class="py-3 px-4 font-mono font-bold" x-text="getEmployeeTotalHours(emp.name) + ' hrs'"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <div x-show="openAddModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4" x-cloak>
        <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl p-6 space-y-4">
            <h3 class="font-bold text-xl text-slate-800 border-b pb-3">Add New Employee</h3>
            <form @submit.prevent="addEmployee" class="space-y-3">
                <input type="text" x-model="newEmp.name" required placeholder="Full Name" class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none">
                <select x-model="newEmp.department" class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none bg-white">
                    <option value="Executive Management">Executive Management</option>
                    <option value="Legal & Compliance">Legal & Compliance</option>
                    <option value="Finance & Accounting">Finance & Accounting</option>
                    <option value="Operations & IT">Operations & IT</option>
                </select>
                <input type="text" x-model="newEmp.position" required placeholder="Position" class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none">
                <input type="email" x-model="newEmp.email" required placeholder="Email" class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none">
                <div class="flex space-x-3 pt-2">
                    <button type="button" @click="openAddModal = false" class="flex-1 py-2.5 bg-slate-100 rounded-xl text-sm font-semibold">Cancel</button>
                    <button type="submit" class="flex-1 py-2.5 bg-brand-blue text-white rounded-xl text-sm font-semibold shadow">Save</button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="openBadgeModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4" x-cloak>
        <div class="bg-white w-full max-w-sm rounded-2xl shadow-2xl p-6 text-center space-y-4">
            <h3 class="font-bold text-lg">QR Badge</h3>
            <div id="qrcode-container" class="p-4 bg-slate-50 rounded-xl inline-block border"></div>
            <button @click="openBadgeModal = false" class="w-full py-2.5 bg-brand-navy text-white rounded-xl text-sm font-semibold">Close</button>
        </div>
    </div>

    <script>
        function adminApp() {
            return {
                isAuthenticated: sessionStorage.getItem('admin_logged_in') === 'true',
                passcode: '',
                currentTab: 'employees',
                mobileMenuOpen: false,
                openAddModal: false,
                openBadgeModal: false,
                selectedEmployee: null,
                searchQuery: '',
                logFilters: { search: '', action: '', date: '' },
                logPage: 1,
                logPerPage: 10,
                newEmp: { name: '', department: 'Legal & Compliance', position: '', email: '' },
                employees: [],
                attendanceLogs: [],

                init() { 
                    if (this.isAuthenticated) {
                        this.fetchData(); 
                    }
                },

                login() {
                    if (this.passcode === 'admin123') {
                        this.isAuthenticated = true;
                        sessionStorage.setItem('admin_logged_in', 'true');
                        this.fetchData();
                    } else {
                        alert('Incorrect passcode. Please try again.');
                        this.passcode = '';
                    }
                },

                logout() {
                    this.isAuthenticated = false;
                    sessionStorage.removeItem('admin_logged_in');
                    this.passcode = '';
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

                getPageTitle() {
                    switch(this.currentTab) {
                        case 'employees': return 'Employee Directory';
                        case 'logs': return 'Attendance History Logs';
                        case 'summary': return 'Payroll DTR Summary';
                        default: return 'Dashboard';
                    }
                },

                async addEmployee() {
                    const newId = this.employees.length > 0 ? Math.max(...this.employees.map(e => e.id)) + 1 : 1;
                    const employee = {
                        id: newId,
                        idCode: `EMP-${100 + newId}`,
                        name: this.newEmp.name,
                        department: this.newEmp.department,
                        position: this.newEmp.position,
                        email: this.newEmp.email
                    };

                    await fetch('api.php?action=add_employee', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(employee)
                    });

                    this.fetchData();
                    this.newEmp = { name: '', department: 'Legal & Compliance', position: '', email: '' };
                    this.openAddModal = false;
                },

                async deleteEmployee(id) {
                    if (confirm('Delete employee?')) {
                        await fetch(`api.php?action=delete_employee&id=${id}`, { method: 'DELETE' });
                        this.fetchData();
                    }
                },

                showBadge(emp) {
                    this.selectedEmployee = emp;
                    this.openBadgeModal = true;
                    setTimeout(() => {
                        const c = document.getElementById('qrcode-container');
                        c.innerHTML = '';
                        new QRCode(c, { text: emp.idCode, width: 150, height: 150 });
                    }, 100);
                },

                get filteredEmployees() {
                    if (!this.searchQuery) return this.employees;
                    return this.employees.filter(e => e.name.toLowerCase().includes(this.searchQuery.toLowerCase()) || e.idCode.toLowerCase().includes(this.searchQuery.toLowerCase()));
                },

                get filteredLogs() {
                    return this.attendanceLogs.filter(log => {
                        const matchesSearch = !this.logFilters.search || 
                            log.employeeName.toLowerCase().includes(this.logFilters.search.toLowerCase()) || 
                            log.department.toLowerCase().includes(this.logFilters.search.toLowerCase());
                        
                        const matchesAction = !this.logFilters.action || log.action === this.logFilters.action;
                        
                        const matchesDate = !this.logFilters.date || log.fullTimestamp.startsWith(this.logFilters.date);

                        return matchesSearch && matchesAction && matchesDate;
                    });
                },

                get paginatedLogs() {
                    const start = (this.logPage - 1) * this.logPerPage;
                    return this.filteredLogs.slice(start, start + this.logPerPage);
                },

                get totalPages() {
                    return Math.ceil(this.filteredLogs.length / this.logPerPage);
                },

                getEmployeeDaysPresent(name) {
                    const logs = this.attendanceLogs.filter(l => l.employeeName === name && l.action === 'Time In');
                    return logs.length > 0 ? logs.length : 1;
                },

                getEmployeeTotalHours(name) {
                    const logs = this.attendanceLogs.filter(l => l.employeeName === name);
                    return (logs.length * 2.5).toFixed(1);
                }
            }
        }
    </script>
</body>
</html>