@extends('layouts.app')

@section('title', 'Our Background Services')

@section('content')
<div class="container-fluid bg-gradient" style="background: linear-gradient(180deg, #1a1a2e 0%, #0f3460 50%, #e94560 100%); min-height: 100vh; padding: 2rem 0;">
    
    <!-- Header -->
    <div class="text-center text-white mb-5">
        <h1 class="display-4 fw-bold mb-3">🌳 Our Hardworking Team 🌳</h1>
        <p class="lead">Watch our dedicated workers tend to your landscape from dawn to dusk</p>
    </div>

    <!-- Day/Night Cycle Indicator -->
    <div class="text-center mb-4">
        <div id="timeIndicator" class="badge fs-5 px-4 py-2">
            <span id="timeIcon">🌅</span>
            <span id="timeText">Dawn</span>
        </div>
    </div>

    <!-- Main Scene Container -->
    <div class="scene-container position-relative mx-auto" style="max-width: 1200px; height: 500px; background: linear-gradient(180deg, #87CEEB 0%, #90EE90 70%, #8B7355 100%); border-radius: 15px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
        
        <!-- Sun/Moon Animation -->
        <div id="celestialBody" class="position-absolute" style="width: 60px; height: 60px; border-radius: 50%; background: #FFD700; box-shadow: 0 0 30px rgba(255, 215, 0, 0.8); transition: all 2s ease-in-out; left: 10%; top: 10%;">
        </div>

        <!-- Trees Container -->
        <div id="treesContainer" class="position-absolute w-100 h-100">
            <!-- Trees will be dynamically added here -->
        </div>

        <!-- Workers Container -->
        <div id="workersContainer" class="position-absolute w-100" style="bottom: 150px;">
            <!-- Workers will be animated here -->
        </div>

        <!-- Ground Layer -->
        <div class="position-absolute w-100" style="bottom: 0; height: 150px; background: linear-gradient(180deg, #90EE90 0%, #8B7355 100%);">
            <!-- Grass blades animation -->
            <div id="grassLayer" class="d-flex justify-content-around align-items-end h-100 px-3">
            </div>
        </div>

        <!-- Particles Effect (trimmed leaves falling) -->
        <div id="particlesContainer" class="position-absolute w-100 h-100" style="pointer-events: none;">
        </div>
    </div>

    <!-- Active Services Status -->
    <div class="row mt-5 px-3">
        <div class="col-12">
            <h3 class="text-white text-center mb-4">🔄 Active Background Services</h3>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card bg-dark text-white border-success">
                <div class="card-body">
                    <h5 class="card-title">
                        <span class="spinner-grow spinner-grow-sm text-success me-2"></span>
                        Staff Workload Balancer
                    </h5>
                    <p class="card-text small">
                        <span id="balancerStatus" class="badge bg-success">Active</span>
                        <span id="balancerCount" class="ms-2">0 bookings balanced</span>
                    </p>
                    <div class="progress" style="height: 5px;">
                        <div id="balancerProgress" class="progress-bar bg-success progress-bar-striped progress-bar-animated" style="width: 0%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card bg-dark text-white border-warning">
                <div class="card-body">
                    <h5 class="card-title">
                        <span class="spinner-grow spinner-grow-sm text-warning me-2"></span>
                        Pollution Notifier
                    </h5>
                    <p class="card-text small">
                        <span id="notifierStatus" class="badge bg-warning text-dark">Monitoring</span>
                        <span id="notifierCount" class="ms-2">0 alerts sent</span>
                    </p>
                    <div class="progress" style="height: 5px;">
                        <div id="notifierProgress" class="progress-bar bg-warning progress-bar-striped progress-bar-animated" style="width: 0%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card bg-dark text-white border-info">
                <div class="card-body">
                    <h5 class="card-title">
                        <span class="spinner-grow spinner-grow-sm text-info me-2"></span>
                        Queue Workers
                    </h5>
                    <p class="card-text small">
                        <span id="queueStatus" class="badge bg-info">Processing</span>
                        <span id="queueCount" class="ms-2">0 jobs processed</span>
                    </p>
                    <div class="progress" style="height: 5px;">
                        <div id="queueProgress" class="progress-bar bg-info progress-bar-striped progress-bar-animated" style="width: 0%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Real-time Activity Log -->
    <div class="row mt-4 px-3">
        <div class="col-12">
            <div class="card bg-dark text-white">
                <div class="card-header">
                    <h5 class="mb-0">📜 Real-time Activity Log</h5>
                </div>
                <div class="card-body" style="max-height: 200px; overflow-y: auto;">
                    <div id="activityLog" class="font-monospace small">
                        <div class="text-success">System initialized... Ready to work! 🌱</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .worker {
        position: absolute;
        width: 40px;
        height: 50px;
        transition: all 0.5s ease;
    }

    .tree {
        position: absolute;
        transition: all 0.8s ease;
    }

    .particle {
        position: absolute;
        width: 8px;
        height: 8px;
        background: #90EE90;
        border-radius: 50%;
        animation: fall linear;
        pointer-events: none;
    }

    @keyframes fall {
        from {
            opacity: 1;
            transform: translateY(0) rotate(0deg);
        }
        to {
            opacity: 0;
            transform: translateY(200px) rotate(360deg);
        }
    }

    .grass-blade {
        width: 3px;
        background: linear-gradient(180deg, #228B22 0%, #90EE90 100%);
        animation: sway 2s ease-in-out infinite;
        transform-origin: bottom;
    }

    @keyframes sway {
        0%, 100% { transform: rotate(-2deg); }
        50% { transform: rotate(2deg); }
    }

    .worker-walking {
        animation: walk 1s steps(4) infinite;
    }

    @keyframes walk {
        0% { background-position: 0; }
        100% { background-position: -160px; }
    }

    .tool-swing {
        animation: swing 0.6s ease-in-out infinite;
        transform-origin: top center;
    }

    @keyframes swing {
        0%, 100% { transform: rotate(-15deg); }
        50% { transform: rotate(15deg); }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuration
    const TREE_COUNT = 8;
    const WORKER_COUNT = 3;
    const DAY_CYCLE_DURATION = 30000; // 30 seconds for full day cycle
    
    let currentTime = 0; // 0 = dawn, 1 = noon, 2 = dusk, 3 = night
    let workers = [];
    let trees = [];
    let activityCount = {
        balancer: 0,
        notifier: 0,
        queue: 0
    };

    // Initialize scene
    initializeTrees();
    initializeWorkers();
    initializeGrass();
    startDayCycle();
    startActivitySimulation();

    function initializeTrees() {
        const container = document.getElementById('treesContainer');
        const treeEmojis = ['🌲', '🌳', '🌴', '🎄'];
        
        for (let i = 0; i < TREE_COUNT; i++) {
            const tree = document.createElement('div');
            tree.className = 'tree';
            tree.innerHTML = treeEmojis[Math.floor(Math.random() * treeEmojis.length)];
            tree.style.fontSize = `${40 + Math.random() * 30}px`;
            tree.style.left = `${10 + (i * (80 / TREE_COUNT))}%`;
            tree.style.bottom = `${150 + Math.random() * 50}px`;
            tree.dataset.health = '100';
            
            container.appendChild(tree);
            trees.push(tree);
        }
    }

    function initializeWorkers() {
        const container = document.getElementById('workersContainer');
        const workerEmojis = ['🧑‍🌾', '👨‍🌾', '👩‍🌾'];
        
        for (let i = 0; i < WORKER_COUNT; i++) {
            const worker = document.createElement('div');
            worker.className = 'worker';
            worker.innerHTML = `<div style="font-size: 40px;">${workerEmojis[i]}</div>`;
            worker.style.left = `${20 + (i * 30)}%`;
            worker.dataset.targetTree = Math.floor(Math.random() * TREE_COUNT);
            worker.dataset.working = 'false';
            
            container.appendChild(worker);
            workers.push(worker);
            
            // Start worker movement
            animateWorker(worker, i);
        }
    }

    function initializeGrass() {
        const container = document.getElementById('grassLayer');
        for (let i = 0; i < 50; i++) {
            const blade = document.createElement('div');
            blade.className = 'grass-blade';
            blade.style.height = `${10 + Math.random() * 20}px`;
            blade.style.animationDelay = `${Math.random() * 2}s`;
            container.appendChild(blade);
        }
    }

    function animateWorker(worker, index) {
        const moveToTree = () => {
            const targetIndex = parseInt(worker.dataset.targetTree);
            const targetTree = trees[targetIndex];
            
            if (!targetTree) return;
            
            const treePos = targetTree.offsetLeft;
            worker.style.left = `${treePos - 20}px`;
            worker.dataset.working = 'true';
            
            setTimeout(() => {
                trimTree(targetTree, index);
                worker.dataset.working = 'false';
                worker.dataset.targetTree = Math.floor(Math.random() * TREE_COUNT);
                setTimeout(moveToTree, 2000 + Math.random() * 3000);
            }, 2000);
        };
        
        setTimeout(moveToTree, index * 1000);
    }

    function trimTree(tree, workerIndex) {
        const health = parseInt(tree.dataset.health);
        const newHealth = Math.max(0, health - 20);
        tree.dataset.health = newHealth;
        
        // Scale tree down slightly
        const currentScale = 1 - ((100 - newHealth) / 200);
        tree.style.transform = `scale(${currentScale})`;
        
        // Create falling particles
        createParticles(tree);
        
        // Log activity
        logActivity(`Worker ${workerIndex + 1} trimmed a tree 🪓`, 'success');
        
        // If tree is fully trimmed, regrow it
        if (newHealth <= 0) {
            setTimeout(() => regrowTree(tree), 3000);
        }
        
        // Update service counters
        updateServiceCounter('balancer');
    }

    function regrowTree(tree) {
        tree.dataset.health = '100';
        tree.style.transform = 'scale(1)';
        logActivity('🌱 A tree has regrown!', 'info');
    }

    function createParticles(tree) {
        const container = document.getElementById('particlesContainer');
        const rect = tree.getBoundingClientRect();
        const containerRect = container.getBoundingClientRect();
        
        for (let i = 0; i < 10; i++) {
            const particle = document.createElement('div');
            particle.className = 'particle';
            particle.style.left = `${rect.left - containerRect.left + Math.random() * 40}px`;
            particle.style.top = `${rect.top - containerRect.top + Math.random() * 40}px`;
            particle.style.animationDuration = `${2 + Math.random() * 2}s`;
            particle.style.background = ['#90EE90', '#228B22', '#8B7355'][Math.floor(Math.random() * 3)];
            
            container.appendChild(particle);
            
            setTimeout(() => particle.remove(), 4000);
        }
    }

    function startDayCycle() {
        const celestialBody = document.getElementById('celestialBody');
        const sceneContainer = document.querySelector('.scene-container');
        const timeIcon = document.getElementById('timeIcon');
        const timeText = document.getElementById('timeText');
        const timeIndicator = document.getElementById('timeIndicator');
        
        const updateCycle = () => {
            const progress = (Date.now() % DAY_CYCLE_DURATION) / DAY_CYCLE_DURATION;
            
            // Move sun/moon across sky
            const xPos = 10 + (progress * 80);
            const yPos = 10 + Math.sin(progress * Math.PI) * -20;
            celestialBody.style.left = `${xPos}%`;
            celestialBody.style.top = `${10 - yPos}%`;
            
            // Change time of day
            if (progress < 0.25) {
                // Dawn
                sceneContainer.style.background = 'linear-gradient(180deg, #FFB347 0%, #FFE5B4 50%, #90EE90 70%, #8B7355 100%)';
                celestialBody.style.background = '#FFD700';
                celestialBody.style.boxShadow = '0 0 30px rgba(255, 215, 0, 0.8)';
                timeIcon.textContent = '🌅';
                timeText.textContent = 'Dawn';
                timeIndicator.className = 'badge fs-5 px-4 py-2 bg-warning text-dark';
            } else if (progress < 0.5) {
                // Day
                sceneContainer.style.background = 'linear-gradient(180deg, #87CEEB 0%, #87CEEB 50%, #90EE90 70%, #8B7355 100%)';
                celestialBody.style.background = '#FFD700';
                celestialBody.style.boxShadow = '0 0 40px rgba(255, 215, 0, 1)';
                timeIcon.textContent = '☀️';
                timeText.textContent = 'Midday';
                timeIndicator.className = 'badge fs-5 px-4 py-2 bg-info';
            } else if (progress < 0.75) {
                // Dusk
                sceneContainer.style.background = 'linear-gradient(180deg, #FF6B6B 0%, #FFE66D 50%, #90EE90 70%, #8B7355 100%)';
                celestialBody.style.background = '#FF6347';
                celestialBody.style.boxShadow = '0 0 30px rgba(255, 99, 71, 0.8)';
                timeIcon.textContent = '🌇';
                timeText.textContent = 'Dusk';
                timeIndicator.className = 'badge fs-5 px-4 py-2 bg-danger';
            } else {
                // Night
                sceneContainer.style.background = 'linear-gradient(180deg, #1a1a2e 0%, #16213e 50%, #0f3460 70%, #533483 100%)';
                celestialBody.style.background = '#F0F0F0';
                celestialBody.style.boxShadow = '0 0 20px rgba(240, 240, 240, 0.6)';
                timeIcon.textContent = '🌙';
                timeText.textContent = 'Night';
                timeIndicator.className = 'badge fs-5 px-4 py-2 bg-dark';
            }
            
            requestAnimationFrame(updateCycle);
        };
        
        updateCycle();
    }

    function startActivitySimulation() {
        // Simulate background service activities
        setInterval(() => {
            const services = ['balancer', 'notifier', 'queue'];
            const service = services[Math.floor(Math.random() * services.length)];
            
            if (Math.random() > 0.5) {
                updateServiceCounter(service);
                
                const messages = {
                    balancer: ['Balanced workload for new booking', 'Assigned staff member to booking', 'Optimized staff distribution'],
                    notifier: ['Checked pollution levels', 'Sent alert notification', 'Environmental scan complete'],
                    queue: ['Processed email job', 'Completed background task', 'Handled webhook event']
                };
                
                logActivity(messages[service][Math.floor(Math.random() * messages[service].length)], service);
            }
        }, 3000);
    }

    function updateServiceCounter(service) {
        activityCount[service]++;
        
        const countElement = document.getElementById(`${service}Count`);
        const progressElement = document.getElementById(`${service}Progress`);
        
        if (countElement) {
            const labels = {
                balancer: 'bookings balanced',
                notifier: 'alerts sent',
                queue: 'jobs processed'
            };
            countElement.textContent = `${activityCount[service]} ${labels[service]}`;
        }
        
        if (progressElement) {
            const progress = Math.min(100, (activityCount[service] * 5) % 100);
            progressElement.style.width = `${progress}%`;
        }
    }

    function logActivity(message, type) {
        const log = document.getElementById('activityLog');
        const timestamp = new Date().toLocaleTimeString();
        const colors = {
            success: 'text-success',
            info: 'text-info',
            balancer: 'text-success',
            notifier: 'text-warning',
            queue: 'text-info'
        };
        
        const entry = document.createElement('div');
        entry.className = colors[type] || 'text-white';
        entry.innerHTML = `<span class="text-muted">[${timestamp}]</span> ${message}`;
        
        log.insertBefore(entry, log.firstChild);
        
        // Keep only last 20 entries
        while (log.children.length > 20) {
            log.removeChild(log.lastChild);
        }
    }
});
</script>
@endsection
