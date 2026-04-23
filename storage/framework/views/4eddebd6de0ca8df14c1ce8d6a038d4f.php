 <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // ==========================================
            // 📡 FETCHING REAL DATA FROM LARAVEL BACKEND
            // ==========================================
            
            const realMonths = <?php echo json_encode($monthlyApplications->pluck('month') ?? []); ?>;
            const realTotals = <?php echo json_encode($monthlyApplications->pluck('total') ?? []); ?>;
            
            const completedCount = <?php echo e($stats->completed_applications ?? 0); ?>;
            const pendingCount   = <?php echo e($stats->pending_applications ?? 0); ?>;
            const failedCount    = <?php echo e($stats->failed ?? 0); ?>; 

            // ==========================================
            // 📊 DRAWING THE CHARTS WITH REAL DATA
            // ==========================================

            // ── 1. VELOCITY LINE CHART ──
            const velocityCtx = document.getElementById('velocityChart').getContext('2d');
            
            let gradient = velocityCtx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(30, 156, 93, 0.4)');
            gradient.addColorStop(1, 'rgba(30, 156, 93, 0.0)'); 

            new Chart(velocityCtx, {
                type: 'line',
                data: {
                    labels: realMonths,
                    datasets: [{
                        label: 'Applications',
                        data: realTotals,
                        borderColor: '#1E9C5D',
                        backgroundColor: gradient,
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#1E9C5D',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { borderDash: [5, 5], color: '#e8ecf0' }, border: {display: false} },
                        x: { grid: { display: false }, border: {display: false} }
                    }
                }
            });

            // ── 2. STATUS DOUGHNUT CHART ──
            const statusCtx = document.getElementById('statusChart').getContext('2d');
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Completed', 'In Progress', 'Failed'],
                    datasets: [{
                        data: [completedCount, pendingCount, failedCount], 
                        backgroundColor: ['#1E9C5D', '#F59E0B', '#EF4444'],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20, font: { family: "'Plus Jakarta Sans', sans-serif" } } }
                    }
                }
            });
            
            // ==========================================
            // 🎁 GIFT TIMELINE HOVER TOOLTIPS
            // ==========================================
            const anchors = document.querySelectorAll('.tooltip-anchor');
            
            anchors.forEach(anchor => {
                const tip = anchor.querySelector('.gm-tooltip');
                if (!tip) return;

                document.body.appendChild(tip);

                function showTip() {
                    const rect = anchor.getBoundingClientRect();
                    const tipW = 220; 
                    const tipH = tip.offsetHeight || 160;
                    const gap = 12;
                    const margin = 10;

                    let left = rect.left + rect.width / 2 - tipW / 2;
                    let top = rect.top - tipH - gap;

                    let safeLeft = Math.max(margin, Math.min(left, window.innerWidth - tipW - margin));
                    
                    const anchorCX = rect.left + rect.width / 2;
                    const arrowX = ((anchorCX - safeLeft) / tipW) * 100;
                    tip.style.setProperty('--arrow-x', Math.max(8, Math.min(92, arrowX)) + '%');

                    tip.style.left = safeLeft + 'px';
                    tip.style.top = top + 'px';
                    tip.classList.add('visible');
                }

                anchor.addEventListener('mouseenter', showTip);
                anchor.addEventListener('mouseleave', () => tip.classList.remove('visible'));
                
                window.addEventListener('beforeunload', () => { 
                    if(tip.parentNode) tip.parentNode.removeChild(tip); 
                });
            });

            // Add this right below the anchors.forEach loop!
            // Hide tooltips instantly if the user scrolls the page or the timeline wrapper
            window.addEventListener('scroll', function() {
                document.querySelectorAll('.gm-tooltip.visible').forEach(tip => {
                    tip.classList.remove('visible');
                });
            }, true); // The "true" ensures it catches scrolls inside containers too

        });
    </script><?php /**PATH /var/www/uat.easytax.live/resources/views/agent/partials/dashboard-js.blade.php ENDPATH**/ ?>